<?php
/**
 * Vista mostrada después de crear una persona desde popup o desde modal (iframe).
 * Notifica al padre/opener para que actualice la lista de clientes; cierra ventana o cierra modal.
 * @var int $id ID de la persona recién creada
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cliente agregado</title>
</head>
<body>
    <p>Cliente cargado correctamente.</p>
    <script>
        (function() {
            var target = window.opener || (window.parent !== window.self ? window.parent : null);
            if (target && typeof target.refreshClientes === 'function') {
                target.refreshClientes(<?= (int)$id ?>);
            }
            if (window.opener) {
                window.close();
            } else if (target && typeof target.cerrarModalNuevoCliente === 'function') {
                target.cerrarModalNuevoCliente();
            }
        })();
    </script>
    <p>Si esta ventana no se cierra sola, puede cerrarla manualmente.</p>
</body>
</html>
