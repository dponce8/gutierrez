<?php 
use app\models\Persona; 
?>

<div class="row" style="padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-4" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Empresa</label>
        <select class="form-control" id="s_sucursal" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Empresas ...</option>
            <?php foreach ($sucursales as $s) {?>
                <option value="<?=$s['idEmpresa']?>"><?=$s['Empresa']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-4" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Ajuste</label>
        <select class="form-control" id="s_tipo_mov" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Ajustes ...</option>
            <?php foreach ($ajustes as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['movimiento']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-4" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Importe</label>
        <input id="importe_ajuste" type="text" class="form-control input_numero">
    </div>
</div>   
<div class="row" style="padding-top: 10px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-12" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Observaciones</label>
        <input id="obs" type="text" class="form-control">
    </div>
</div>   

<div id="d_ajuste_guarda"></div>

<script>
    $(document).ready(function() {
        document.getElementById("ajusteTitulo").innerHTML="Carga Ajuste para <b><?=Persona::findOne(['id' => $id])->apellido.' '.Persona::findOne(['id' => $id])->nombre?></b>"; 
    });      

    $(".input_numero").on({
        "focus": function (event) {
            $(event.target).select();
        },
        "keyup": function (event) {
            $(event.target).val(function (index, value ) {
                return value.replace(/[^0-9.,\-]/g, "");
            });
        }
    });

    function cargarAjuste() {
        var goOn =1;
        var mensaje = "";

        if ($('#s_sucursal').val() == 0) {
            goOn = 0; mensaje = "Seleccione Sucursal.<br>"; 
        }
        if ($('#s_tipo_mov').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Ajuste.<br>"; 
        }
        if ($('#importe_ajuste').val() == '0' || $('#importe_ajuste').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese importe del ajuste.<br>"; 
        }

        if (goOn == 1) {
            Swal.fire({
                title: 'Ajuste',
                text: '¿Cargar Ajuste?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Cargar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("index.php?r=site/cuenta-ajuste-guarda&id=" + <?=$id?>+"&sucursal=" + $('#s_sucursal').val() +
                    "&tipo=" + $('#s_tipo_mov').val() +"&importe=" + $('#importe_ajuste').val().replace(/\./g, '').replace(/,/g, '.') +"&obs=" + $('#obs').val() 
                    , function (response) {
                        jQuery("#d_ajuste_guarda").html(response);
                    });
                }
            })    
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ajuste',
                html: mensaje
            });
        }    
    }
</script>