<?php

namespace app\commands;

use Yii;
use DOMDocument;
use Exception;
use PDO;
use SimpleXMLElement;
use SoapClient;

class AfipWsaaService
{
    private $certificado;
    private $clavePrivada;
    private $service;
    private $wsaaUrl;
    private $cuit;

    public function __construct(string $certificado, $clavePrivada,  $cuit,  $service = 'wsfe',  
    $wsaaUrl = 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms?wsdl')
    {
        // Validar que los certificados no estén vacíos
        if (empty($certificado) || trim($certificado) === '') {
            throw new Exception("El certificado no puede estar vacío. Verifique que el certificado se haya cargado correctamente desde el archivo o la base de datos.");
        }
        
        if (empty($clavePrivada) || trim($clavePrivada) === '') {
            throw new Exception("La clave privada no puede estar vacía. Verifique que la clave privada se haya cargado correctamente desde el archivo o la base de datos.");
        }
        
        $this->certificado = $certificado;
        $this->clavePrivada = $clavePrivada;
        $this->cuit = $cuit;
        $this->service = $service;
        $this->wsaaUrl = $wsaaUrl;
    }

    public function obtenerTA(): array
    {
        $ta = $this->obtenerTADesdeBase();

        if ($ta && $this->taVigente($ta)) {
            return $ta;
        }

        $traXml = $this->generarTRA();
        $cms = $this->firmarTRA($traXml);
        $taXml = $this->enviarCMS($cms);
        $ta = $this->parsearTA($taXml);

        $this->guardarTANueva($ta);

        return $ta;
    }

    private function generarTRA(): string
    {
        $tra = new DOMDocument('1.0', 'UTF-8');

        $loginTicketRequest = $tra->createElement('loginTicketRequest');
        $loginTicketRequest->setAttribute('version', '1.0');
        $tra->appendChild($loginTicketRequest);

        $header = $tra->createElement('header');
        $loginTicketRequest->appendChild($header);

        $header->appendChild($tra->createElement('uniqueId', time()));
        $header->appendChild($tra->createElement('generationTime', date('c', time() - 60)));
        $header->appendChild($tra->createElement('expirationTime', date('c', time() + 60)));

        $loginTicketRequest->appendChild($tra->createElement('service', $this->service));

        return $tra->saveXML();
    }

    private function firmarTRA(string $traXml): string
    {
        $traFile = tempnam(sys_get_temp_dir(), 'TRA');
        $cmsFile = tempnam(sys_get_temp_dir(), 'CMS');
        $crtFile = tempnam(sys_get_temp_dir(), 'CRT');
        $keyFile = tempnam(sys_get_temp_dir(), 'KEY');

        file_put_contents($traFile, $traXml);
        
        // Limpiar y validar certificado
        $certificadoLimpio = $this->limpiarPEM($this->certificado, 'CERTIFICATE');
        if (empty($certificadoLimpio)) {
            $certPreview = substr($this->certificado, 0, 200);
            $certLength = strlen($this->certificado);
            throw new Exception("El certificado está vacío o no tiene formato PEM válido.\n" .
                "Longitud del certificado original: {$certLength} caracteres\n" .
                "Contenido (primeros 200 chars): " . ($certPreview ?: '(vacío)'));
        }
        file_put_contents($crtFile, $certificadoLimpio);
        
        // Limpiar y validar clave privada
        $claveLimpia = $this->limpiarPEM($this->clavePrivada, 'PRIVATE KEY');
        if (empty($claveLimpia)) {
            $keyPreview = substr($this->clavePrivada, 0, 100);
            $keyLength = strlen($this->clavePrivada);
            throw new Exception("La clave privada está vacía o no tiene formato PEM válido.\n" .
                "Longitud de la clave original: {$keyLength} caracteres\n" .
                "Contenido (primeros 100 chars): " . ($keyPreview ?: '(vacío)'));
        }
        file_put_contents($keyFile, $claveLimpia);

        $cmd = "openssl smime -sign -signer {$crtFile} -inkey {$keyFile} -in {$traFile} -out {$cmsFile} -outform DER -nodetach";
        exec($cmd . ' 2>&1', $output, $resultCode);

        if ($resultCode !== 0) {
            // Guardar contenido de los archivos para depuración antes de eliminarlos
            $certContent = file_get_contents($crtFile);
            $keyContentPreview = substr(file_get_contents($keyFile), 0, 100);
            
            unlink($traFile);
            unlink($cmsFile);
            unlink($crtFile);
            unlink($keyFile);
            
            throw new Exception("Error al firmar el TRA con OpenSSL:\n" . implode("\n", $output) . 
                "\n\nCertificado (primeros 200 chars): " . substr($certContent, 0, 200) .
                "\nClave privada (primeros 100 chars): " . $keyContentPreview);
        }

        $cms = file_get_contents($cmsFile);

        unlink($traFile);
        unlink($cmsFile);
        unlink($crtFile);
        unlink($keyFile);

        return $cms;
    }

    private function enviarCMS(string $cms): string
    {
        $client = new SoapClient($this->wsaaUrl, [
            'trace' => true,
            'exceptions' => true
        ]);

        $params = ['in0' => base64_encode($cms)];
        $response = $client->loginCms($params);

        return $response->loginCmsReturn;
    }

    private function parsearTA(string $taXml): array
    {
        $xml = new SimpleXMLElement($taXml);
        return [
            'token' => (string)$xml->credentials->token,
            'sign' => (string)$xml->credentials->sign,
            'expiration' => strtotime((string)$xml->header->expirationTime),
            'xml' => $taXml,
        ];
    }

    private function taVigente(array $ta): bool
    {
        return isset($ta['expiration']) && time() < ($ta['expiration'] - 60);
    }

    private function obtenerTADesdeBase(): ?array
    {
        $db = Yii::$app->db;
        $command = $db->createCommand("SELECT token, sign, ta_xml, expiration FROM afip_ta WHERE cuit = :cuit AND service = :service", [
            ':cuit' => $this->cuit,
            ':service' => $this->service
        ]);

        $row = $command->queryOne();

        if (!$row) return null;

        return [
            'token' => $row['token'],
            'sign' => $row['sign'],
            'expiration' => strtotime($row['expiration']),
            'xml' => $row['ta_xml']
        ];
    }

    private function guardarTANueva(array $ta): void
    {
        $db = Yii::$app->db;
        $db->createCommand()->upsert('afip_ta', [
            'cuit' => $this->cuit,
            'service' => $this->service,
            'token' => $ta['token'],
            'sign' => $ta['sign'],
            'ta_xml' => $ta['xml'],
            'expiration' => date('Y-m-d H:i:s', $ta['expiration']),
            'actualizado_en' => date('Y-m-d H:i:s')
        ])->execute();
    }

    private function limpiarPEM(string $contenido, string $tipo = 'CERTIFICATE'): string
    {
        if (empty($contenido) || trim($contenido) === '') {
            return '';
        }
        
        // Decodifica si vino como JSON escapado
        $contenido = str_replace(['\\n', '\r'], "\n", $contenido);
        
        // Normaliza saltos de línea
        $contenido = preg_replace("/\r\n|\r/", "\n", $contenido);
        
        // Elimina espacios al inicio y final
        $contenido = trim($contenido);
        
        // Si después de limpiar está vacío, retornar vacío
        if (empty($contenido)) {
            return '';
        }
        
        // Define los marcadores según el tipo
        $beginMarkers = [
            'CERTIFICATE' => ['-----BEGIN CERTIFICATE-----', '-----BEGIN TRUSTED CERTIFICATE-----'],
            'PRIVATE KEY' => ['-----BEGIN PRIVATE KEY-----', '-----BEGIN RSA PRIVATE KEY-----', '-----BEGIN ENCRYPTED PRIVATE KEY-----']
        ];
        
        $endMarkers = [
            'CERTIFICATE' => ['-----END CERTIFICATE-----', '-----END TRUSTED CERTIFICATE-----'],
            'PRIVATE KEY' => ['-----END PRIVATE KEY-----', '-----END RSA PRIVATE KEY-----', '-----END ENCRYPTED PRIVATE KEY-----']
        ];
        
        $markers = $tipo === 'CERTIFICATE' ? $beginMarkers['CERTIFICATE'] : $beginMarkers['PRIVATE KEY'];
        $endMarkersList = $tipo === 'CERTIFICATE' ? $endMarkers['CERTIFICATE'] : $endMarkers['PRIVATE KEY'];
        
        // Busca el inicio del PEM
        $beginPos = false;
        $beginMarker = null;
        foreach ($markers as $marker) {
            $pos = stripos($contenido, $marker);
            if ($pos !== false) {
                $beginPos = $pos;
                $beginMarker = $marker;
                break;
            }
        }
        
        if ($beginPos === false) {
            // Si no encuentra el marcador, intenta agregarlo
            // Esto puede pasar si el contenido viene sin las líneas de inicio/fin
            // Pero solo si el contenido parece ser base64 (caracteres alfanuméricos, +, /, =)
            $contenidoLimpio = preg_replace('/\s+/', '', $contenido);
            if (preg_match('/^[A-Za-z0-9+\/=\s]+$/', $contenidoLimpio) && strlen($contenidoLimpio) > 50) {
                $beginMarker = $tipo === 'CERTIFICATE' ? '-----BEGIN CERTIFICATE-----' : '-----BEGIN PRIVATE KEY-----';
                $contenido = $beginMarker . "\n" . $contenido;
                $beginPos = 0;
            } else {
                // Si no parece ser un certificado válido, retornar vacío
                return '';
            }
        }
        
        // Busca el final del PEM
        $endPos = false;
        $endMarker = null;
        foreach ($endMarkersList as $marker) {
            $pos = stripos($contenido, $marker);
            if ($pos !== false) {
                $endPos = $pos;
                $endMarker = $marker;
                break;
            }
        }
        
        if ($endPos === false) {
            // Si no encuentra el marcador final, intenta agregarlo
            // Solo si ya encontramos el inicio
            if ($beginPos !== false) {
                $endMarker = $tipo === 'CERTIFICATE' ? '-----END CERTIFICATE-----' : '-----END PRIVATE KEY-----';
                $contenido = $contenido . "\n" . $endMarker;
                $endPos = strlen($contenido) - strlen($endMarker);
            } else {
                return '';
            }
        } else {
            // Extrae solo el contenido entre los marcadores
            $contenido = substr($contenido, $beginPos, $endPos - $beginPos + strlen($endMarker));
        }
        
        // Normaliza los marcadores al formato estándar
        if ($tipo === 'CERTIFICATE') {
            $contenido = preg_replace('/-----BEGIN (TRUSTED )?CERTIFICATE-----/', '-----BEGIN CERTIFICATE-----', $contenido);
            $contenido = preg_replace('/-----END (TRUSTED )?CERTIFICATE-----/', '-----END CERTIFICATE-----', $contenido);
        } else {
            // Para claves privadas, mantiene el formato original si es RSA o ENCRYPTED
            if (stripos($contenido, 'RSA PRIVATE KEY') !== false) {
                $contenido = preg_replace('/-----BEGIN (RSA )?PRIVATE KEY-----/', '-----BEGIN RSA PRIVATE KEY-----', $contenido);
                $contenido = preg_replace('/-----END (RSA )?PRIVATE KEY-----/', '-----END RSA PRIVATE KEY-----', $contenido);
            } else {
                $contenido = preg_replace('/-----BEGIN (ENCRYPTED )?PRIVATE KEY-----/', '-----BEGIN PRIVATE KEY-----', $contenido);
                $contenido = preg_replace('/-----END (ENCRYPTED )?PRIVATE KEY-----/', '-----END PRIVATE KEY-----', $contenido);
            }
        }
        
        // Normaliza saltos de línea múltiples
        $contenido = preg_replace("/\n{3,}/", "\n\n", $contenido);
        
        // Asegura que termine con un salto de línea
        if (substr($contenido, -1) !== "\n") {
            $contenido .= "\n";
        }
        
        // Validación final: debe contener al menos los marcadores y algo de contenido
        if (stripos($contenido, 'BEGIN') === false || stripos($contenido, 'END') === false) {
            return '';
        }
        
        return $contenido;
    }
}
