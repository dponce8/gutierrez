# 🔐 Configuración de Certificados AFIP

## 📂 Estructura de Directorios

```
certificates/afip/
├── produccion/
│   ├── cert.pem    # Certificado de producción de AFIP
│   └── key.pem     # Clave privada de producción
└── testing/
    ├── cert.pem    # Certificado de testing de AFIP
    └── key.pem     # Clave privada de testing
```

## 🚀 Para Configurar Producción

### 1. Obtener Certificados de AFIP
- Accede al portal de AFIP con tu clave fiscal
- Ve a "Administrador de Relaciones de Clave Fiscal" → "Certificados"
- Genera o descarga tus certificados para "Servicio Web de Facturación Electrónica"

### 2. Colocar Archivos
```bash
# Copiar certificado de producción
cp /ruta/a/tu/certificado.crt certificates/afip/produccion/cert.pem

# Copiar clave privada de producción  
cp /ruta/a/tu/clave.key certificates/afip/produccion/key.pem
```

### 3. Configurar Permisos de Seguridad
```bash
# Solo lectura para el propietario
chmod 600 certificates/afip/produccion/*.pem

# Solo el usuario de la aplicación puede acceder
chown www-data:www-data certificates/afip/produccion/*.pem
```

### 4. Cambiar a Producción
En `CobroController.php`, cambiar:
```php
// DE:
$wsfeUrl = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL'; // Homologación

// A:
$wsfeUrl = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL'; // Producción
```

## 🧪 Para Testing (Ya Configurado)

Los certificados de testing ya están configurados y funcionando.
Si necesitas cambiarlos, coloca tus certificados de testing en:
- `certificates/afip/testing/cert.pem`
- `certificates/afip/testing/key.pem`

## 🔒 Seguridad

### ✅ Buenas Prácticas Implementadas:
- ✅ Certificados en archivos separados del código
- ✅ Detección automática de ambiente (producción vs testing)
- ✅ Validación de existencia de certificados en producción
- ✅ Logging de qué certificados se están usando
- ✅ Fallback a certificados hardcodeados solo en testing

### 🛡️ Recomendaciones de Seguridad:
- 🔐 Usar permisos restrictivos (600) en archivos de certificados
- 🚫 Nunca subir certificados de producción al repositorio
- 📝 Agregar `certificates/afip/produccion/` al .gitignore
- 🔄 Rotar certificados según políticas de AFIP
- 📊 Monitorear logs para detectar problemas de certificados

## 🔍 Verificar Configuración

```bash
# Ver qué certificados se están usando
tail -f runtime/logs/afip_cae.log | grep "certificado\|clave"

# Verificar permisos
ls -la certificates/afip/produccion/
```

## 🆘 Troubleshooting

### Error: "Certificados AFIP de producción no encontrados"
1. Verificar que los archivos existen en `certificates/afip/produccion/`
2. Verificar permisos de lectura
3. Verificar que el contenido de los archivos sea válido

### Error: "Invalid certificate format"
1. Verificar que los archivos tienen formato PEM correcto
2. Asegurarse de que incluyen las líneas BEGIN/END
3. Verificar que no hay caracteres extraños al inicio/final

## 📞 Soporte
Para problemas con certificados AFIP:
- Portal AFIP: https://www.afip.gob.ar/
- Mesa de ayuda AFIP: 0800-999-AFIP (2347)
