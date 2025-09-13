<div class="row">
    <div class="form-group col-sm-4" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Cliente</label>
        <select class="form-control my-chosen-select" id="s_cliente" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Clientes ...</option>
            <?php foreach ($clientes as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['apellido'].' '.$p['nombre']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Salida</label>
        <input id="fecha_salida" type="date" class="form-control" value="<?=date("Y-m-d")?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_salida" type="time" class="form-control" value="<?=date("H:i")?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Regreso</label>
        <input id="fecha_regreso" type="date" class="form-control" value="<?=date("Y-m-d")?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hora</label>
        <input id="hora_regreso" type="time" class="form-control" value="<?=date("H:i")?>">
    </div>
</div> 
<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Pasajeros</label>
        <input id="pasajeros" type="text" class="form-control">
    </div>
    <div class="form-group col-sm-5" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Origen</label>
        <select class="form-control my-chosen-select" id="s_origen" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Origen ...</option>
            <?php foreach ($localidades as $p) {?>
                <option value="<?=$p['idlocalidad']?>"><?='['.$p['pais'].'] '.$p['provincia'].' - '.$p['localidad']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-5" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Destino</label>
        <select class="form-control my-chosen-select" id="s_destino" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Destino ...</option>
            <?php foreach ($localidades as $p) {?>
                <option value="<?=$p['idlocalidad']?>"><?='['.$p['pais'].'] '.$p['provincia'].' - '.$p['localidad']?></option>
            <?php } ?>
        </select>
    </div>
</div> 
<div class="row">
    <div class="form-group col-sm-6" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Dirección Origen</label>
        <input id="direccion_origen" type="text" class="form-control">
    </div>
    <div class="form-group col-sm-6" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Dirección Destino</label>
        <input id="direccion_destino" type="text" class="form-control">
    </div>
</div> 

<div class="row">
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Total Viaje</label>
        <input id="total" type="text" class="form-control">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Vto</label>
        <input id="fecha_vto" type="date" class="form-control" value="<?=date("Y-m-d")?>">
    </div>
    <div class="form-group col-sm-8" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Observaciones</label>
        <input id="obs" type="text" class="form-control">
    </div>
</div> 

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
    });      


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
                    "&direccion_origen=" + $('#direccion_origen').val() +
                    "&direccion_destino=" + $('#direccion_destino').val() +
                    "&total=" + $('#total').val() +
                    "&obs=" + $('#obs').val() +
                    "&pasajeros=" + $('#pasajeros').val() +
                    "&fecha_vto=" + $('#fecha_vto').val() +
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