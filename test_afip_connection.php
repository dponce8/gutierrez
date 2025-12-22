<?php
/**
 * Script de diagnóstico de conectividad con AFIP
 * Ejecutar desde el servidor: php test_afip_connection.php
 */

echo "========================================\n";
echo "DIAGNÓSTICO DE CONECTIVIDAD CON AFIP\n";
echo "========================================\n\n";

$url = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx';
$host = 'servicios1.afip.gov.ar';
$port = 443;

$tests = [];
$errors = [];

// Test 1: Resolución DNS
echo "1. RESOLUCIÓN DNS\n";
echo "   Host: $host\n";
$ip = @gethostbyname($host);
if ($ip !== $host) {
    echo "   ✓ DNS OK: $host -> $ip\n";
    $tests['dns'] = true;
    
    // Resolución inversa
    $hostname = @gethostbyaddr($ip);
    if ($hostname) {
        echo "   ✓ Reverse DNS: $ip -> $hostname\n";
    }
} else {
    echo "   ✗ DNS FALLÓ: No se pudo resolver $host\n";
    $tests['dns'] = false;
    $errors[] = "DNS no resuelve";
}
echo "\n";

// Test 2: Conectividad TCP
echo "2. CONECTIVIDAD TCP\n";
echo "   Probando conexión a $host:$port...\n";
$tcpConnection = @fsockopen($host, $port, $errno, $errstr, 10);
if ($tcpConnection) {
    fclose($tcpConnection);
    echo "   ✓ TCP OK: Puerto $port accesible\n";
    $tests['tcp'] = true;
} else {
    echo "   ✗ TCP FALLÓ: Error $errno - $errstr\n";
    $tests['tcp'] = false;
    $errors[] = "TCP: Error $errno - $errstr";
}
echo "\n";

// Test 3: Conectividad SSL
echo "3. CONECTIVIDAD SSL\n";
echo "   Probando conexión SSL a $host:$port...\n";
$sslConnection = @fsockopen("ssl://$host", $port, $errno, $errstr, 10);
if ($sslConnection) {
    fclose($sslConnection);
    echo "   ✓ SSL OK: Conexión SSL establecida\n";
    $tests['ssl'] = true;
} else {
    echo "   ✗ SSL FALLÓ: Error $errno - $errstr\n";
    $tests['ssl'] = false;
    $errors[] = "SSL: Error $errno - $errstr";
}
echo "\n";

// Test 4: file_get_contents
echo "4. TEST CON file_get_contents\n";
echo "   URL: $url\n";
$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'method' => 'GET',
        'user_agent' => 'PHP Diagnostic Script',
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);
$startTime = microtime(true);
$result = @file_get_contents($url, false, $context);
$duration = round((microtime(true) - $startTime) * 1000, 2);
if ($result !== false) {
    $length = strlen($result);
    echo "   ✓ OK: Recibidos $length bytes en {$duration}ms\n";
    $tests['file_get_contents'] = true;
} else {
    $error = error_get_last();
    echo "   ✗ FALLÓ: " . ($error['message'] ?? 'Error desconocido') . "\n";
    $tests['file_get_contents'] = false;
    $errors[] = "file_get_contents: " . ($error['message'] ?? 'Error desconocido');
}
echo "\n";

// Test 5: cURL
echo "5. TEST CON cURL\n";
if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_NOBODY => true,
        CURLOPT_HEADER => true,
    ]);
    $startTime = microtime(true);
    $result = curl_exec($ch);
    $duration = round((microtime(true) - $startTime) * 1000, 2);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    curl_close($ch);
    
    if ($result !== false && $httpCode > 0) {
        echo "   ✓ OK: HTTP $httpCode en {$duration}ms\n";
        echo "   Tiempo total: " . round($totalTime * 1000, 2) . "ms\n";
        $tests['curl'] = true;
    } else {
        echo "   ✗ FALLÓ: $error\n";
        $tests['curl'] = false;
        $errors[] = "cURL: $error";
    }
} else {
    echo "   ⚠ cURL no disponible\n";
    $tests['curl'] = null;
}
echo "\n";

// Test 6: SoapClient
echo "6. TEST CON SoapClient\n";
if (class_exists('SoapClient')) {
    try {
        $wsdlUrl = $url . '?WSDL';
        echo "   WSDL: $wsdlUrl\n";
        $startTime = microtime(true);
        $client = new SoapClient($wsdlUrl, [
            'connection_timeout' => 15,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ])
        ]);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        echo "   ✓ OK: Cliente SOAP creado en {$duration}ms\n";
        $tests['soap'] = true;
    } catch (Exception $e) {
        echo "   ✗ FALLÓ: " . $e->getMessage() . "\n";
        $tests['soap'] = false;
        $errors[] = "SoapClient: " . $e->getMessage();
    }
} else {
    echo "   ✗ SoapClient no disponible (extensión SOAP no instalada)\n";
    $tests['soap'] = false;
    $errors[] = "SoapClient no disponible";
}
echo "\n";

// Test 7: Variables de Proxy
echo "7. CONFIGURACIÓN DE PROXY\n";
$proxyVars = ['HTTP_PROXY', 'HTTPS_PROXY', 'http_proxy', 'https_proxy', 'NO_PROXY', 'no_proxy'];
$proxyFound = false;
foreach ($proxyVars as $var) {
    $value = getenv($var);
    if ($value) {
        echo "   ✓ $var = $value\n";
        $proxyFound = true;
    }
}
if (!$proxyFound) {
    echo "   ✓ No hay variables de proxy configuradas\n";
}
echo "\n";

// Test 8: Información del Sistema
echo "8. INFORMACIÓN DEL SISTEMA\n";
echo "   PHP Version: " . phpversion() . "\n";
echo "   OS: " . PHP_OS . "\n";
echo "   Server API: " . php_sapi_name() . "\n";

echo "\n   Extensiones relevantes:\n";
$extensions = ['soap', 'curl', 'openssl', 'xml', 'mbstring'];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✓' : '✗';
    echo "   $status $ext: " . ($loaded ? 'Instalada' : 'NO instalada') . "\n";
}

echo "\n   Configuración SSL/TLS:\n";
if (function_exists('openssl_get_cert_locations')) {
    $locations = openssl_get_cert_locations();
    echo "   default_cert_file: " . ($locations['default_cert_file'] ?? 'N/A') . "\n";
    echo "   default_cert_file_env: " . ($locations['default_cert_file_env'] ?? 'N/A') . "\n";
    echo "   default_cert_dir: " . ($locations['default_cert_dir'] ?? 'N/A') . "\n";
}

echo "\n   Timeouts:\n";
echo "   default_socket_timeout: " . ini_get('default_socket_timeout') . "s\n";
echo "   max_execution_time: " . ini_get('max_execution_time') . "s\n";
echo "\n";

// Resumen
echo "========================================\n";
echo "RESUMEN\n";
echo "========================================\n";
$passed = 0;
$failed = 0;
$skipped = 0;

foreach ($tests as $test => $result) {
    if ($result === true) {
        $passed++;
        echo "✓ $test: OK\n";
    } elseif ($result === false) {
        $failed++;
        echo "✗ $test: FALLÓ\n";
    } else {
        $skipped++;
        echo "⚠ $test: NO DISPONIBLE\n";
    }
}

echo "\nTotal: $passed OK, $failed FALLÓ, $skipped NO DISPONIBLE\n";

if (count($errors) > 0) {
    echo "\nERRORES ENCONTRADOS:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\n";

// Recomendaciones
if ($failed > 0) {
    echo "RECOMENDACIONES:\n";
    if (!$tests['dns']) {
        echo "  - Verificar configuración DNS: cat /etc/resolv.conf\n";
        echo "  - Probar con DNS alternativo (8.8.8.8)\n";
    }
    if (!$tests['tcp']) {
        echo "  - Verificar firewall: sudo ufw status\n";
        echo "  - Verificar conectividad: ping $host\n";
    }
    if (!$tests['ssl']) {
        echo "  - Verificar certificados CA: sudo update-ca-certificates\n";
        echo "  - Verificar extensión OpenSSL: php -m | grep openssl\n";
    }
    if (!$tests['soap']) {
        echo "  - Instalar extensión SOAP: sudo apt-get install php8.2-soap\n";
        echo "  - Reiniciar PHP-FPM: sudo systemctl restart php8.2-fpm\n";
    }
    if ($tests['curl'] === false) {
        echo "  - Verificar extensión cURL: php -m | grep curl\n";
        echo "  - Verificar proxy si es necesario\n";
    }
}

echo "\n";

