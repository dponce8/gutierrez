# Solución para Error "dh key too small" con OpenSSL 3.0

## Problema

El error `OpenSSL/3.0.13: error:0A00018A:SSL routines::dh key too small` ocurre porque OpenSSL 3.0 y versiones posteriores son más estrictos con los parámetros de seguridad SSL/TLS. El servidor de AFIP utiliza parámetros Diffie-Hellman (DH) que OpenSSL 3.0 considera demasiado pequeños según los estándares modernos de seguridad.

## Solución Implementada

Se ha actualizado la configuración SSL en tres lugares:

1. **CajaController.php** - Para conexiones SOAP al servicio WSFEv1
2. **AfipWsaaService.php** - Para conexiones SOAP al servicio WSAA
3. **test_afip_connection.php** - Script de diagnóstico actualizado

### Cambios Realizados

#### 1. Configuración de Cipher Suite

Se cambió de:
```php
'ciphers' => 'ALL:!ADH:!LOW:!EXP:!MD5:@STRENGTH',
```

A:
```php
'ciphers' => 'DEFAULT@SECLEVEL=1:ALL:!aNULL:!eNULL:!MD5:!3DES:!DES:!RC4:!IDEA:!SEED:!aDSS:!SRP:!PSK',
```

El parámetro clave es `DEFAULT@SECLEVEL=1` que reduce el nivel de seguridad de OpenSSL para permitir conexiones con servidores que usan parámetros DH más antiguos.

#### 2. Método Crypto Específico

Se especificó explícitamente TLS 1.2:
```php
'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
```

#### 3. Versión Mínima de Protocolo

```php
'min_proto_version' => STREAM_CRYPTO_PROTO_TLSv1_2,
```

## Niveles de Seguridad de OpenSSL

OpenSSL 3.0+ usa niveles de seguridad (security levels):

- **SECLEVEL 0**: Más permisivo, acepta algoritmos antiguos
- **SECLEVEL 1**: Permisivo, acepta algoritmos modernos y algunos antiguos (recomendado para AFIP)
- **SECLEVEL 2**: Moderado, rechaza algoritmos antiguos
- **SECLEVEL 3**: Estricto, solo algoritmos modernos
- **SECLEVEL 4**: Muy estricto, solo algoritmos más modernos

El nivel 1 es el recomendado para mantener compatibilidad con servidores como AFIP que aún usan parámetros DH antiguos, mientras se mantiene un nivel de seguridad razonable.

## Verificación

Después de aplicar los cambios, ejecutar el script de prueba:

```bash
php test_afip_connection.php
```

Deberías ver que todos los tests pasan, especialmente:
- ✓ SSL OK
- ✓ cURL OK
- ✓ SoapClient OK

## Alternativa: Configuración Global del Sistema (NO RECOMENDADO)

Si necesitas una solución temporal a nivel del sistema (no recomendado por seguridad), puedes crear un archivo de configuración OpenSSL:

```bash
sudo nano /etc/ssl/openssl.cnf
```

Y agregar al final:
```
[system_default_sect]
MinProtocol = TLSv1.2
CipherString = DEFAULT@SECLEVEL=1
```

**NOTA**: Esto afecta a todas las conexiones SSL del sistema y reduce la seguridad global. Es mejor usar la configuración por aplicación como se implementó.

## Referencias

- [OpenSSL 3.0 Migration Guide](https://www.openssl.org/docs/man3.0/man7/migration_guide.html)
- [OpenSSL Security Levels](https://www.openssl.org/docs/man3.0/man7/SSL_CTX_set_security_level.html)
- [PHP Stream Context SSL Options](https://www.php.net/manual/en/context.ssl.php)

## Notas de Seguridad

- La configuración `SECLEVEL=1` reduce la seguridad pero es necesaria para la compatibilidad con AFIP
- Se mantiene la verificación de certificados deshabilitada (`verify_peer => false`) para evitar problemas con certificados intermedios
- Se recomienda monitorear actualizaciones de AFIP que puedan mejorar sus parámetros SSL

