<div class="row">
    <div class="form-group col-sm-4" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Cliente <a href="#" onclick="abrirNuevoCliente(); return false;" title="Agregar nuevo cliente" style="text-decoration: none;"><i class="fa fa-plus" style="font-size: 10px;"></i></a></label>
        <select class="form-control my-chosen-select" id="s_cliente" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Clientes ...</option>
            <?php foreach ($clientes as $p) {?>
                <option value="<?=$p['id']?>" <?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) && $infoPresupuesto['id_cliente'] == $p['id']) ? 'selected' : ''?>><?=$p['apellido'].' '.$p['nombre']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Salida</label>
        <input id="fecha_salida" type="date" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? date("Y-m-d", strtotime($infoPresupuesto['fecha_salida'])) : date("Y-m-d"))?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_salida" type="time" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? date("H:i", strtotime($infoPresupuesto['hora_salida'])) : date("H:i"))?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Regreso</label>
        <input id="fecha_regreso" type="date" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? date("Y-m-d", strtotime($infoPresupuesto['fecha_regreso'])) : date("Y-m-d"))?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_regreso" type="time" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? date("H:i", strtotime($infoPresupuesto['hora_regreso'])) : date("H:i"))?>">
    </div>
</div> 
<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Pasajeros</label>
        <input id="pasajeros" type="text" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? $infoPresupuesto['pasajeros'] : '')?>">
    </div>
    <div class="form-group col-sm-5" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Origen <a href="#" onclick="abrirNuevoOrigen(); return false;" title="Agregar nueva localidad como origen" style="text-decoration: none;"><i class="fa fa-plus" style="font-size: 10px;"></i></a></label>
        <select class="form-control my-chosen-select" id="s_origen" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Origen ...</option>
            <?php foreach ($localidades as $p) {?>
                <option value="<?=$p['idlocalidad']?>" <?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) && $infoPresupuesto['origen'] == $p['idlocalidad']) ? 'selected' : ''?>><?='['.$p['pais'].'] '.$p['provincia'].' - '.$p['localidad']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-5" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Destino <a href="#" onclick="abrirNuevoDestino(); return false;" title="Agregar nueva localidad como destino" style="text-decoration: none;"><i class="fa fa-plus" style="font-size: 10px;"></i></a></label>
        <select class="form-control my-chosen-select" id="s_destino" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px; width: 100%;" >
            <option value="0">Destino ...</option>
            <?php foreach ($localidades as $p) {?>
                <option value="<?=$p['idlocalidad']?>" <?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) && $infoPresupuesto['destino'] == $p['idlocalidad']) ? 'selected' : ''?>><?='['.$p['pais'].'] '.$p['provincia'].' - '.$p['localidad']?></option>
            <?php } ?>
        </select>
    </div>
</div> 
<div class="row">
    <div class="form-group col-sm-6" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Dirección Origen</label>
        <input id="direccion_origen" type="text" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? $infoPresupuesto['direccion_origen'] : '')?>">
    </div>
    <div class="form-group col-sm-6" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Dirección Destino</label>
        <input id="direccion_destino" type="text" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? $infoPresupuesto['direccion_destino'] : '')?>">
    </div>
</div> 

<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Total Viaje</label>
        <input id="total" type="text" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? $infoPresupuesto['total'] : '')?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Vto</label>
        <input id="fecha_vto" type="date" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? date("Y-m-d", strtotime($infoPresupuesto['fecha_vto'])) : date("Y-m-d"))?>">
    </div>
    <div class="form-group col-sm-8" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Programación Turística</label>
        <input id="obs" type="text" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? $infoPresupuesto['obs'] : '')?>">
    </div>
</div> 

<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Tipo Unidad</label>
        <select class="form-control" id="s_tipo_unidad" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Tipo ...</option>
            <?php foreach ($tipoCoche as $p) {?>
                <option value="<?=$p['id']?>" <?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) && $infoPresupuesto['id_tipo_coche'] == $p['id']) ? 'selected' : ''?>><?=$p['tipo']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-10" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Observaciones Internas</label>
        <input id="obs_interna" type="text" class="form-control" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? $infoPresupuesto['obs_interna'] : '')?>">
    </div>
</div> 

<input id="h_id_presupuesto" type="hidden" value="<?=(isset($infoPresupuesto) && !is_null($infoPresupuesto) ? $infoPresupuesto['id'] : 0)?>">

<style>
    /* Chosen copia el ancho del select al iniciar; si el layout no calculó bien las columnas, queda muy estrecho */
    #s_cliente_chosen, #s_origen_chosen, #s_destino_chosen { width: 100% !important; min-width: 0; }
</style>

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen({ width: '100%' });
    });

    function abrirNuevoCliente() {
        var url = '<?= \yii\helpers\Url::to(['persona/create', 'popup' => 1]) ?>';
        document.getElementById('iframeNuevoCliente').src = url;
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


    function cargarPresupuesto() {
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
        if ($('#s_origen').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Origen.<br>"; 
        }
        if ($('#s_destino').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Destino.<br>"; 
        }
        if ($('#s_tipo_unidad').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Tipo Unidad.<br>"; 
        }
        if ($('#direccion_origen').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Dirección de Origen.<br>"; 
        }
        if ($('#direccion_destino').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Dirección de Destino.<br>"; 
        }
        if ($('#total').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Total Viaje.<br>"; 
        }
        if ($('#pasajeros').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Pasajeros.<br>"; 
        }
        if ($('#fecha_vto').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Fecha de Vto.<br>"; 
        }

        if (goOn == 1) {
            Swal.fire({
                title: 'Presupuestos',
                text: '¿Registrar Operación?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("index.php?r=viaje/presupuesto-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() +
                    "&cliente=" + $('#s_cliente').val() +
                    "&fecha_salida=" + $('#fecha_salida').val() +
                    "&hora_salida=" + $('#hora_salida').val() +
                    "&fecha_regreso=" + $('#fecha_regreso').val() +
                    "&hora_regreso=" + $('#hora_regreso').val() +
                    "&origen=" + $('#s_origen').val() +
                    "&destino=" + $('#s_destino').val() +
                    "&tipo_unidad=" + $('#s_tipo_unidad').val() +
                    "&direccion_origen=" + $('#direccion_origen').val() +
                    "&direccion_destino=" + $('#direccion_destino').val() +
                    "&total=" + $('#total').val() +
                    "&obs=" + $('#obs').val() +
                    "&obs_interna=" + $('#obs_interna').val() +
                    "&pasajeros=" + $('#pasajeros').val() +
                    "&fecha_vto=" + $('#fecha_vto').val() +
                    "&id_presupuesto=" + $('#h_id_presupuesto').val() +
                    "&guardar=1"
                    , function (response) {
                        jQuery("#d_presupuesto_lista").html(response);
                    });                    
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Presupuestos',
                html: mensaje
            });
        }  
    }
</script>