<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Salida</label>
        <input id="fecha_salida" type="date" class="form-control" value="<?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['fecha_salida']) {echo date("Y-m-d",strtotime($infoViaje['fecha_salida']));} else {echo date("Y-m-d");} ?>" >
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_salida" type="time" class="form-control" value="<?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['hora_salida']) {echo $infoViaje['hora_salida'];} else {echo date("H:i");} ?>" >
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Regreso</label>
        <input id="fecha_regreso" type="date" class="form-control" value="<?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['fecha_regreso']) {echo date("Y-m-d",strtotime($infoViaje['fecha_regreso']));} else {echo date("Y-m-d");} ?>" >
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_regreso" type="time" class="form-control" value="<?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['hora_regreso']) {echo $infoViaje['hora_regreso'];} else {echo date("H:i");} ?>" >
    </div>
</div> 
<div class="row">
    <div class="form-group col-sm-2" style="margin-top: 0px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Coche</label>
        <select class="form-control my-chosen-select" id="s_coche" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Coche ...</option>
            <?php foreach ($coches as $p) {?>
                <option value="<?=$p['id']?>" <?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['id_vehiculo'] == $p['id']) {echo 'selected';}?>><?=$p['numero_interno'].' ['.$p['asientos'].']'?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-4" style="margin-top: 0px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione 1° Chofer</label>
        <select class="form-control my-chosen-select" id="s_chofer_1" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Chofer ...</option>
            <?php foreach ($choferes as $p) {?>
                <option value="<?=$p['idempleado']?>" <?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['id_chofer_1'] == $p['idempleado']) {echo 'selected';}?>><?=$p['chofer']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-4" style="margin-top: 0px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione 2° Chofer</label>
        <select class="form-control my-chosen-select" id="s_chofer_2" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Chofer ...</option>
            <?php foreach ($choferes as $p) {?>
                <option value="<?=$p['idempleado']?>" <?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['id_chofer_2'] == $p['idempleado']) {echo 'selected';}?>><?=$p['chofer']?></option>
            <?php } ?>
        </select>
    </div>
</div> 

<div class="row">
    <div class="form-group col-sm-12" style="margin-top: 0px; ">
        <label style="font-size: 11px; ">Observaciones</label>
        <input id="obs" type="text" class="form-control" <?php if (isset($infoViaje) && !empty($infoViaje) && $infoViaje['obs']) {echo 'value="'.$infoViaje['obs'].'"';}?>>
    </div>
</div> 

<input type="hidden" id="h_id_viaje" value="<?=$infoViaje['id']?>">

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
        $("#btnCargarViaje").show();
    });      


    function guardarModViaje() {
        var goOn = 1;
        var mensaje = '';

        if ($('#fecha_salida').val() == '') {
            goOn = 0; mensaje = "Seleccione Fecha de Salida.<br>"; 
        }
        if ($('#fecha_regreso').val() == '') {
            goOn = 0; mensaje = "Seleccione Fecha de Regreso.<br>"; 
        }
        if ($('#hora_salida').val() == '') {
            goOn = 0; mensaje = "Seleccione Hora de Salida.<br>"; 
        }
        if ($('#hora_regreso').val() == '') {
            goOn = 0; mensaje = "Seleccione Hora de Regreso.<br>"; 
        }
        
        // Validar que fecha/hora de regreso sea posterior a fecha/hora de salida
        if ($('#fecha_salida').val() != '' && $('#hora_salida').val() != '' && 
            $('#fecha_regreso').val() != '' && $('#hora_regreso').val() != '') {
            
            var fechaHoraSalida = new Date($('#fecha_salida').val() + 'T' + $('#hora_salida').val());
            var fechaHoraRegreso = new Date($('#fecha_regreso').val() + 'T' + $('#hora_regreso').val());
            
            if (fechaHoraRegreso <= fechaHoraSalida) {
                goOn = 0; 
                mensaje += "La fecha y hora de regreso debe ser posterior a la fecha y hora de salida.<br>";
            }
        }

        if ($('#s_coche').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Coche.<br>"; 
        }
        if ($('#s_chofer_1').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione 1° Chofer.<br>"; 
        }

        if (goOn == 1) {
            Swal.fire({
                title: 'Viajes',
                text: '¿Modificar Viaje?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("index.php?r=viaje/viaje-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() +
                    "&idViaje=" + $('#h_id_viaje').val() +
                    "&coche=" + $('#s_coche').val() +
                    "&chofer_1=" + $('#s_chofer_1').val() +
                    "&chofer_2=" + $('#s_chofer_2').val() +
                    "&obs=" + $('#obs').val() +
                    "&modificar=1"
                    , function (response) {
                        try {
                            // Intentar parsear como JSON primero
                            var resultado = JSON.parse(response);
                            
                            if (resultado.success === false) {
                                // Mostrar errores de validación
                                var mensajeErrores = '<ul>';
                                resultado.errores.forEach(function(error) {
                                    mensajeErrores += '<li>' + error + '</li>';
                                });
                                mensajeErrores += '</ul>';
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al Guardar Viaje',
                                    html: resultado.mensaje + '<br><br><strong>Detalles:</strong>' + mensajeErrores,
                                    confirmButtonText: 'Entendido'
                                });
                            } else if (resultado.success === true) {
                                // Viaje guardado exitosamente
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Viaje Guardado',
                                    text: resultado.mensaje,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Cerrar modal y actualizar lista
                                    $('#movModal').modal('hide');
                                    // Actualizar la lista de viajes
                                    $.post("index.php?r=viaje/viaje-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val(), function (response) {
                                        jQuery("#d_viaje_lista").html(response);
                                    });
                                });
                            }
                        } catch (e) {
                            // Si no es JSON, es la respuesta HTML normal (para compatibilidad)
                            jQuery("#d_viaje_lista").html(response);
                            
                            // Mostrar mensaje de éxito genérico
                            Swal.fire({
                                icon: 'success',
                                title: 'Viaje Guardado',
                                text: 'El viaje se ha guardado correctamente.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                $('#movModal').modal('hide');
                            });
                        }
                    });                    
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Viajes Especiales',
                html: mensaje
            });
        }  
    }
</script>