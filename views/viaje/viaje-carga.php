<div class="row">
    <div class="form-group col-sm-4" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Cliente <a href="#" onclick="abrirNuevoCliente(); return false;" title="Agregar nuevo cliente" style="text-decoration: none;"><i class="fa fa-plus" style="font-size: 10px;"></i></a></label>
        <select class="form-control my-chosen-select" id="s_cliente" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Clientes ...</option>
            <?php foreach ($clientes as $p) {?>
                <option value="<?=$p['id']?>" <?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['id_cliente'] == $p['id']) {echo 'selected';}?>><?=$p['apellido'].' '.$p['nombre']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Salida</label>
        <input id="fecha_salida" type="date" class="form-control" value="<?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['fecha_salida']) {echo date("Y-m-d",strtotime($presupuestos['fecha_salida']));} else {echo date("Y-m-d");} ?>" >
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_salida" type="time" class="form-control" value="<?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['hora_salida']) {echo $presupuestos['hora_salida'];} else {echo date("H:i");} ?>" >
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Regreso</label>
        <input id="fecha_regreso" type="date" class="form-control" value="<?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['fecha_regreso']) {echo date("Y-m-d",strtotime($presupuestos['fecha_regreso']));} else {echo date("Y-m-d");} ?>" >
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_regreso" type="time" class="form-control" value="<?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['hora_regreso']) {echo $presupuestos['hora_regreso'];} else {echo date("H:i");} ?>" >
    </div>
</div> 
<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Pasajeros</label>
        <input id="pasajeros" type="text" class="form-control" <?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['pasajeros']) {echo 'value="'.$presupuestos['pasajeros'].'"';}?>>
    </div>
    <div class="form-group col-sm-5" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Origen <a href="#" onclick="abrirNuevoOrigen(); return false;" title="Agregar nueva localidad como origen" style="text-decoration: none;"><i class="fa fa-plus" style="font-size: 10px;"></i></a></label>
        <select class="form-control my-chosen-select" id="s_origen" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Origen ...</option>
            <?php foreach ($localidades as $p) {?>
                <option value="<?=$p['idlocalidad']?>" <?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['origen'] == $p['idlocalidad']) {echo 'selected';}?>><?='['.$p['pais'].'] '.$p['provincia'].' - '.$p['localidad']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-5" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Destino <a href="#" onclick="abrirNuevoDestino(); return false;" title="Agregar nueva localidad como destino" style="text-decoration: none;"><i class="fa fa-plus" style="font-size: 10px;"></i></a></label>
        <select class="form-control my-chosen-select" id="s_destino" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Destino ...</option>
            <?php foreach ($localidades as $p) {?>
                <option value="<?=$p['idlocalidad']?>" <?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['destino'] == $p['idlocalidad']) {echo 'selected';}?>><?='['.$p['pais'].'] '.$p['provincia'].' - '.$p['localidad']?></option>
            <?php } ?>
        </select>
    </div>
</div> 
<div class="row">
    <div class="form-group col-sm-4" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Dirección Origen</label>
        <input id="direccion_origen" type="text" class="form-control" <?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['direccion_origen']) {echo 'value="'.$presupuestos['direccion_origen'].'"';}?>>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Coordenadas</label>
        <input id="coord_origen" type="text" class="form-control" >
    </div>
    <div class="form-group col-sm-4" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Dirección Destino</label>
        <input id="direccion_destino" type="text" class="form-control" <?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['direccion_destino']) {echo 'value="'.$presupuestos['direccion_destino'].'"';}?>>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Coordenadas</label>
        <input id="coord_destino" type="text" class="form-control">
    </div>
</div> 

<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Empresa</label>
        <select class="form-control" id="s_empresa" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Empresa ...</option>
            <?php foreach ($empresas as $p) {?>
                <option value="<?=$p['idEmpresa']?>"><?=$p['Empresa']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Coche</label>
        <select class="form-control my-chosen-select" id="s_coche" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Coche ...</option>
            <?php foreach ($coches as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['numero_interno'].' ['.$p['asientos'].']'?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-4" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione 1° Chofer</label>
        <select class="form-control my-chosen-select" id="s_chofer_1" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Chofer ...</option>
            <?php foreach ($choferes as $p) {?>
                <option value="<?=$p['idempleado']?>"><?=$p['chofer']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-4" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione 2° Chofer</label>
        <select class="form-control my-chosen-select" id="s_chofer_2" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Chofer ...</option>
            <?php foreach ($choferes as $p) {?>
                <option value="<?=$p['idempleado']?>"><?=$p['chofer']?></option>
            <?php } ?>
        </select>
    </div>
</div> 

<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Total Viaje</label>
        <input id="total" type="text" class="form-control" <?php if (isset($presupuestos) && !empty($presupuestos) && $presupuestos['total']) {echo 'value="'.$presupuestos['total'].'"';}?>>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Anticipo</label>
        <input id="anticipo" type="text" class="form-control">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Medio</label>
        <select class="form-control" id="s_medio" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Medios ...</option>
            <?php foreach ($medios as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['medio']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-6" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Observaciones</label>
        <input id="obs" type="text" class="form-control">
    </div>
</div> 

<style>
    /* Chosen: forzar ancho 100% en los contenedores para que se vea el contenido */
    #s_cliente_chosen, #s_origen_chosen, #s_destino_chosen, #s_coche_chosen, #s_chofer_1_chosen, #s_chofer_2_chosen { width: 100% !important; min-width: 0; }
</style>

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen({ width: '100%' });
        $("#btnCargarViaje").show();
    });

    function abrirNuevoCliente() {
        document.getElementById('iframeNuevoCliente').src = '<?= \yii\helpers\Url::to(['persona/create', 'popup' => 1]) ?>';
        $('#modalNuevoCliente').modal('show');
    }

    function abrirNuevoOrigen() {
        window.tipoLocalidadModal = 'origen';
        document.getElementById('iframeNuevoLocalidad').src = '<?= \yii\helpers\Url::to(['localidades/create', 'popup' => 1]) ?>';
        $('#modalNuevoLocalidad').modal('show');
    }

    function abrirNuevoDestino() {
        window.tipoLocalidadModal = 'destino';
        document.getElementById('iframeNuevoLocalidad').src = '<?= \yii\helpers\Url::to(['localidades/create', 'popup' => 1]) ?>';
        $('#modalNuevoLocalidad').modal('show');
    }      


    function cargarViaje() {
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
        if ($('#s_origen').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Origen.<br>"; 
        }
        if ($('#s_destino').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Destino.<br>"; 
        }
        if ($('#direccion_origen').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Dirección de Origen.<br>"; 
        }
        if ($('#direccion_destino').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Dirección de Destino.<br>"; 
        }
        if ($('#s_chofer_1').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione 1° Chofer.<br>"; 
        }
        /*if ($('#s_chofer_2').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione 2° Chofer.<br>"; 
        }*/
        if ($('#total').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Total Viaje.<br>"; 
        }
        if ($('#pasajeros').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Pasajeros.<br>"; 
        }
        if ($('#s_empresa').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Empresa.<br>"; 
        }
        if ($('#anticipo').val() != '' && $('#s_medio').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Medio de Pago para Anticipo.<br>"; 
        }

        if (goOn == 1) {
            Swal.fire({
                title: 'Viajes Especiales',
                text: '¿Registrar Operación?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("index.php?r=viaje/viaje-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() +
                    "&cliente=" + $('#s_cliente').val() +
                    "&fecha_salida=" + $('#fecha_salida').val() +
                    "&hora_salida=" + $('#hora_salida').val() +
                    "&fecha_regreso=" + $('#fecha_regreso').val() +
                    "&hora_regreso=" + $('#hora_regreso').val() +
                    "&origen=" + $('#s_origen').val() +
                    "&destino=" + $('#s_destino').val() +
                    "&direccion_origen=" + $('#direccion_origen').val() +
                    "&direccion_destino=" + $('#direccion_destino').val() +
                    "&coord_origen=" + $('#coord_origen').val() +
                    "&coord_destino=" + $('#coord_destino').val() +
                    "&chofer_1=" + $('#s_chofer_1').val() +
                    "&chofer_2=" + $('#s_chofer_2').val() +
                    "&total=" + $('#total').val() +
                    "&anticipo=" + $('#anticipo').val() +
                    "&medio=" + $('#s_medio').val() +
                    "&obs=" + $('#obs').val() +
                    "&coche=" + $('#s_coche').val() +
                    "&pasajeros=" + $('#pasajeros').val() +
                    "&empresa=" + $('#s_empresa').val() +
                    "&guardar=1"
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