<?php
/**
 * Vista mostrada después de crear una localidad desde popup o desde modal (iframe).
 * Notifica al padre para que actualice los selects de origen/destino y cierra el modal.
 * @var int $IdLocalidad ID de la localidad recién creada
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Localidad agregada</title>
</head>
<body>
    <p>Localidad cargada correctamente.</p>
    <script>
        (function() {
            var id = <?= (int)$IdLocalidad ?>;
            var target = window.opener || (window.parent !== window.self ? window.parent : null);
            if (target && typeof target.refreshLocalidades === 'function') {
                target.refreshLocalidades(id);
            }
            if (window.opener) {
                window.close();
            } else if (target && typeof target.cerrarModalNuevoLocalidad === 'function') {
                target.cerrarModalNuevoLocalidad();
            }
        })();
    </script>
    <p>Si esta ventana no se cierra sola, puede cerrarla manualmente.</p>
</body>
</html>
