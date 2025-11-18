# Archivos WSDL de AFIP

Esta carpeta contiene los archivos WSDL de los servicios web de AFIP descargados localmente.

## Archivos

- **wsaa.wsdl**: Servicio de autenticación (WSAA)
- **wsfev1.wsdl**: Servicio de facturación electrónica (WSFEV1) - Producción
- **wsfev1-homo.wsdl**: Servicio de facturación electrónica (WSFEV1) - Homologación/Testing

## ¿Por qué usar archivos WSDL locales?

En servidores de producción en la nube pueden existir:
- Restricciones de firewall
- Problemas con certificados SSL/TLS
- Configuraciones de PHP que impiden cargar WSDL remotos
- Problemas de latencia o conectividad

Usar archivos WSDL locales:
- ✅ Evita problemas de conectividad
- ✅ Mejora el rendimiento (no descarga el WSDL cada vez)
- ✅ Garantiza disponibilidad aunque AFIP tenga problemas

## Actualizar los archivos

Si AFIP actualiza sus servicios, puedes actualizar los archivos con:

```bash
cd wsdl/afip

# WSAA
curl -o wsaa.wsdl "https://wsaa.afip.gov.ar/ws/services/LoginCms?wsdl"

# WSFEV1 Producción
curl -o wsfev1.wsdl "https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL"

# WSFEV1 Homologación
curl -o wsfev1-homo.wsdl "https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL"
```

## Implementación

El código en `CobroController.php` usa la función `getWsdlPath()` que automáticamente:
1. Busca el archivo WSDL local
2. Si existe, lo usa
3. Si no existe, usa la URL remota como fallback

Esto asegura compatibilidad en todos los entornos (local y producción).

## Solución de problemas

### Error: "Could not connect to host"

Si recibes este error en producción, puede deberse a:

1. **Firewall del servidor**: El servidor en la nube bloquea conexiones HTTPS salientes
   - Solución: Contactar al administrador para habilitar conexiones a dominios de AFIP
   - Dominios a permitir: `*.afip.gov.ar`
   - Puertos: 443 (HTTPS)

2. **Extensiones PHP faltantes**: Verificar que estén instaladas
   ```bash
   php -m | grep -E "(soap|openssl|curl)"
   ```

3. **Configuración de red**: El servidor puede requerir configuración de proxy
   - Agregar configuración de proxy en el stream_context si es necesario

### Error: "dh key too small" o "SSL routines::dh key too small"

Este error ocurre en servidores con OpenSSL 3.x que tienen requisitos de seguridad más estrictos.

**Causa**: Los servidores de AFIP (especialmente producción) usan claves Diffie-Hellman consideradas "pequeñas" por OpenSSL 3.x.

**Solución aplicada**: El código ahora incluye la configuración:
```php
'ciphers' => 'DEFAULT@SECLEVEL=1',
'security_level' => 1
```

Esto reduce el nivel de seguridad SSL de 2 (default en OpenSSL 3.x) a 1, permitiendo claves DH más pequeñas necesarias para conectarse a AFIP. Es seguro para este caso de uso específico ya que AFIP es una entidad confiable.

### Script de diagnóstico

Ejecutar el script `diagnostico-afip.php` en el servidor para identificar el problema:
```bash
php diagnostico-afip.php
```

Este script verifica:
- Extensiones PHP instaladas
- Conectividad con servidores de AFIP
- Archivos WSDL locales
- Certificados AFIP
- Configuración PHP

