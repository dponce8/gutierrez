# Diagnóstico y Solución de Problemas de Conectividad con AFIP

## Problema
Error: "Could not connect to host" al intentar conectarse a `servicios1.afip.gov.ar` desde el servidor de producción Ubuntu.

## Diagnóstico Paso a Paso

### 1. Verificar Conectividad Básica

```bash
# Verificar si el servidor tiene acceso a internet
ping -c 4 8.8.8.8

# Verificar resolución DNS
nslookup servicios1.afip.gov.ar
# o
host servicios1.afip.gov.ar
# o
dig servicios1.afip.gov.ar
```

### 2. Verificar Conectividad al Servidor de AFIP

```bash
# Verificar conectividad TCP al puerto 443
telnet servicios1.afip.gov.ar 443
# o
nc -zv servicios1.afip.gov.ar 443
# o
timeout 10 bash -c "</dev/tcp/servicios1.afip.gov.ar/443" && echo "Conexión exitosa" || echo "Conexión fallida"
```

### 3. Verificar con cURL

```bash
# Probar conexión HTTPS
curl -v -k --connect-timeout 10 https://servicios1.afip.gov.ar/wsfev1/service.asmx

# Ver información detallada
curl -I -k --connect-timeout 10 https://servicios1.afip.gov.ar/wsfev1/service.asmx
```

### 4. Verificar Firewall

```bash
# Verificar reglas de firewall (UFW)
sudo ufw status
sudo ufw status numbered

# Verificar iptables
sudo iptables -L -n -v | grep 443
sudo iptables -L OUTPUT -n -v

# Verificar si hay bloqueos
sudo iptables -L -n | grep -i afip
```

### 5. Verificar Proxy

```bash
# Verificar variables de entorno de proxy
env | grep -i proxy

# Verificar configuración de proxy en PHP
php -r "var_dump(getenv('HTTP_PROXY'));"
php -r "var_dump(getenv('HTTPS_PROXY'));"

# Verificar configuración de proxy del sistema
cat /etc/environment | grep -i proxy
cat ~/.bashrc | grep -i proxy
```

### 6. Verificar Extensiones PHP

```bash
# Verificar extensiones SSL/SOAP instaladas
php -m | grep -i ssl
php -m | grep -i soap
php -m | grep -i curl
php -m | grep -i openssl

# Ver información de OpenSSL
php -r "var_dump(openssl_get_cert_locations());"
php -r "echo openssl_error_string();"
```

### 7. Verificar Certificados SSL del Sistema

```bash
# Verificar certificados CA instalados
ls -la /etc/ssl/certs/ | head
ls -la /usr/local/share/ca-certificates/

# Actualizar certificados CA
sudo update-ca-certificates

# Verificar si hay certificados específicos necesarios
openssl s_client -connect servicios1.afip.gov.ar:443 -showcerts
```

### 8. Verificar Logs del Sistema

```bash
# Ver logs de PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log
# o según tu versión
sudo tail -f /var/log/php-fpm.log

# Ver logs de Apache/Nginx
sudo tail -f /var/log/apache2/error.log
# o
sudo tail -f /var/log/nginx/error.log

# Ver logs de la aplicación
tail -f /home/consultora.dp.8/gutierrez/runtime/logs/afip_cae.log
```

## Soluciones Comunes

### Solución 1: Permitir Tráfico Saliente en Firewall

Si usas UFW:
```bash
sudo ufw allow out 443/tcp
sudo ufw allow out 80/tcp
sudo ufw reload
```

Si usas iptables directamente:
```bash
sudo iptables -A OUTPUT -p tcp --dport 443 -j ACCEPT
sudo iptables -A OUTPUT -p tcp --dport 80 -j ACCEPT
sudo iptables-save
```

### Solución 2: Configurar Proxy (si es necesario)

Si el servidor está detrás de un proxy corporativo:

```bash
# Configurar variables de entorno en /etc/environment
sudo nano /etc/environment

# Agregar:
HTTP_PROXY=http://proxy.example.com:8080
HTTPS_PROXY=http://proxy.example.com:8080
NO_PROXY=localhost,127.0.0.1

# O configurar solo para PHP-FPM
sudo nano /etc/php/8.2/fpm/php.ini
# Agregar en la sección [PHP]:
; Proxy settings
http_proxy = "http://proxy.example.com:8080"
https_proxy = "http://proxy.example.com:8080"
```

Luego reiniciar PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

### Solución 3: Actualizar Certificados CA

```bash
# Actualizar certificados
sudo apt-get update
sudo apt-get install ca-certificates
sudo update-ca-certificates

# Verificar certificados
openssl s_client -connect servicios1.afip.gov.ar:443 -CApath /etc/ssl/certs/
```

### Solución 4: Verificar Configuración de Red

```bash
# Verificar configuración de red
ip addr show
ip route show

# Verificar DNS
cat /etc/resolv.conf

# Probar con DNS alternativo
echo "nameserver 8.8.8.8" | sudo tee /etc/resolv.conf
echo "nameserver 8.8.4.4" | sudo tee -a /etc/resolv.conf
```

### Solución 5: Verificar Timeout y Límites

```bash
# Verificar límites del sistema
ulimit -a

# Verificar configuración de PHP
php -i | grep -i timeout
php -i | grep -i default_socket_timeout
```

### Solución 6: Probar Conexión desde PHP Directamente

Crear un script de prueba:

```php
<?php
// test_afip_connection.php
$url = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx';

echo "Probando conexión a: $url\n\n";

// Test 1: file_get_contents
echo "1. Test con file_get_contents:\n";
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET',
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);
$result = @file_get_contents($url, false, $context);
if ($result !== false) {
    echo "   ✓ OK\n";
} else {
    echo "   ✗ FALLÓ: " . error_get_last()['message'] . "\n";
}

// Test 2: cURL
echo "\n2. Test con cURL:\n";
if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $result = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($result !== false && $httpCode > 0) {
        echo "   ✓ OK (HTTP $httpCode)\n";
    } else {
        echo "   ✗ FALLÓ: $error\n";
    }
} else {
    echo "   ✗ cURL no disponible\n";
}

// Test 3: SoapClient
echo "\n3. Test con SoapClient:\n";
if (class_exists('SoapClient')) {
    try {
        $client = new SoapClient($url . '?WSDL', [
            'connection_timeout' => 10,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ]);
        echo "   ✓ OK\n";
    } catch (Exception $e) {
        echo "   ✗ FALLÓ: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ SoapClient no disponible\n";
}

// Test 4: DNS
echo "\n4. Test de resolución DNS:\n";
$host = 'servicios1.afip.gov.ar';
$ip = gethostbyname($host);
if ($ip !== $host) {
    echo "   ✓ DNS OK: $host -> $ip\n";
} else {
    echo "   ✗ DNS FALLÓ: No se pudo resolver $host\n";
}

// Test 5: Socket
echo "\n5. Test de socket TCP:\n";
$host = 'servicios1.afip.gov.ar';
$port = 443;
$connection = @fsockopen($host, $port, $errno, $errstr, 5);
if ($connection) {
    fclose($connection);
    echo "   ✓ Socket OK\n";
} else {
    echo "   ✗ Socket FALLÓ: Error $errno - $errstr\n";
}
```

Ejecutar:
```bash
php test_afip_connection.php
```

## Verificación Final

Después de aplicar las soluciones, verificar:

```bash
# 1. Verificar conectividad
curl -I -k https://servicios1.afip.gov.ar/wsfev1/service.asmx

# 2. Verificar desde PHP
php -r "var_dump(file_get_contents('https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL'));"

# 3. Revisar logs de la aplicación
tail -f /home/consultora.dp.8/gutierrez/runtime/logs/afip_cae.log
```

## Comandos Útiles para Depuración

```bash
# Ver todas las conexiones de red activas
sudo netstat -tulpn | grep :443
sudo ss -tulpn | grep :443

# Ver procesos que escuchan en puertos
sudo lsof -i :443

# Ver tráfico de red (requiere tcpdump)
sudo tcpdump -i any -n host servicios1.afip.gov.ar

# Verificar ruta de red
traceroute servicios1.afip.gov.ar
mtr servicios1.afip.gov.ar
```

## Contacto con el Proveedor de Hosting

Si ninguna de las soluciones funciona, puede ser un problema del proveedor de hosting. Preguntar:

1. ¿Hay firewall bloqueando conexiones salientes HTTPS?
2. ¿Se requiere configuración de proxy?
3. ¿Hay restricciones de red para dominios .gov.ar?
4. ¿Se puede hacer un test de conectividad desde el servidor?

## Notas Importantes

- El código ahora incluye diagnósticos automáticos que se registran en el log
- Revisar siempre el archivo de log: `/home/consultora.dp.8/gutierrez/runtime/logs/afip_cae.log`
- Los diagnósticos incluyen: DNS, TCP, SSL, cURL y detección de proxy
- El timeout se aumentó a 120 segundos para conexiones lentas

