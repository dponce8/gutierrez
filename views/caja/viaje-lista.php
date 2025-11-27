<?php
// Validar CUIT: debe tener exactamente 13 dígitos y solo contener números
$cuitValido = false;
if (isset($cuit) && $cuit !== null && $cuit !== '') {
    // Verificar que tenga exactamente 11 caracteres y que todos sean dígitos
    // Limpiar la variable $cuit quitando puntos, guiones y espacios en blanco antes de validarla
    if (isset($cuit) && $cuit !== null && $cuit !== '') {
        $cuit = preg_replace('/[^\d]/', '', $cuit);
    }
    if (strlen($cuit) == 11 && ctype_digit($cuit)) {
        $cuitValido = true;
    }
}
?>
<label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Viaje</label>
<select class="form-control" id="s_viaje" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
    <option value="0">Viajes ...</option>
    <?php foreach ($listado as $t) {
        $fechaSalida = '';
        $fechaRegreso = '';
        
        if (isset($t['fecha_salida']) && $t['fecha_salida'] !== null && $t['fecha_salida'] !== '') {
            $fechaSalida = date("d/m/Y", strtotime($t['fecha_salida']));
            if (isset($t['hora_salida']) && $t['hora_salida'] !== null) {
                $fechaSalida .= ' ' . $t['hora_salida'];
            }
        }
        
        if (isset($t['fecha_regreso']) && $t['fecha_regreso'] !== null && $t['fecha_regreso'] !== '') {
            $fechaRegreso = date("d/m/Y", strtotime($t['fecha_regreso']));
            if (isset($t['hora_regreso']) && $t['hora_regreso'] !== null) {
                $fechaRegreso .= ' ' . $t['hora_regreso'];
            }
        }
        
        $fechas = '';
        if ($fechaSalida !== '' || $fechaRegreso !== '') {
            $fechas = ' (' . $fechaSalida . ($fechaSalida !== '' && $fechaRegreso !== '' ? ' - ' : '') . $fechaRegreso . ')';
        }
    ?>
        <option value="<?=$t['id']?>"><?='[N° '.$t['id'].'] '.$t['local_origen'].' -> '.$t['local_destino'].' - Total: '.number_format($t['total'], 2, ',', '.').' - Pendiente: '.number_format((floatval($t['total']) - floatval($t['importe_pagado'])), 2, ',', '.').$fechas?></option>
    <?php } ?>
</select>

<script>
$(document).ready(function() {
    console.log('CUIT: <?=$cuit?>');
    // Si el CUIT no es válido, deshabilitar opciones 1, 2 y 3 del select s_factura
    <?php if (!$cuitValido): ?>
    var cuitValido = false;
    <?php else: ?>
    var cuitValido = true;
    <?php endif; ?>
    
    if (!cuitValido) {
        // Deshabilitar opciones con id 1, 2 y 3 en el select s_factura
        $('#s_factura option[value="1"]').prop('disabled', true);
        $('#s_factura option[value="2"]').prop('disabled', true);
        $('#s_factura option[value="3"]').prop('disabled', true);
        
        // Si alguna de estas opciones está seleccionada, cambiar a la opción por defecto
        if ($('#s_factura').val() == '1' || $('#s_factura').val() == '2' || $('#s_factura').val() == '3') {
            $('#s_factura').val('0');
        }
    } else {
        // Si el CUIT es válido, habilitar todas las opciones
        $('#s_factura option[value="1"]').prop('disabled', false);
        $('#s_factura option[value="2"]').prop('disabled', false);
        $('#s_factura option[value="3"]').prop('disabled', false);
    }
});
</script>

