<div class="row" style="padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Empresa</label>
        <select class="form-control" id="s_caja_ch" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Empresas ...</option>
            <?php foreach ($cajas as $s) {?>
                <option value="<?=$s['idEmpresa']?>"><?=$s['Empresa']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Tipo</label>
        <select class="form-control " id="s_tipo" onChange="setChequePersona()" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Tipos ...</option>
            <?php foreach ($tipo as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['tipo']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-3" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" id="lab_1">Seleccione Persona</label>
        <select class="form-control" id="s_persona_ch" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Personas ...</option>
            <?php foreach ($personas as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['persona']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Nro. Cheque</label>
        <input id="numero" type="text" class="form-control input_numero">
    </div>
    <div class="form-group col-sm-3" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Importe</label>
        <input id="importe_ch" type="text" class="form-control input_numero">
    </div>
</div>   

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Banco</label>
        <select class="form-control " id="s_banco" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Bancos ...</option>
            <?php foreach ($bancos as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['banco']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-3" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Librador</label>
        <input id="librador" type="text" class="form-control">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Fecha Pago</label>
        <input id="pago" type="date" class="form-control" value="<?=date("Y-m-d")?>">
    </div>
    
    <div class="form-group col-sm-5" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Observaciones</label>
        <input id="obs" type="text" class="form-control ">
    </div>
</div>  

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Formato cheque</label>
        <select class="form-control " id="s_formato" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Formatos ...</option>
            <?php foreach ($formatos as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['formato']?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Tipo Transmisión</label>
        <select class="form-control " id="s_orden" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Transmisión ...</option>
            <?php foreach ($tiposOrden as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['tipo']?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group col-sm-2" style="padding-top: 5px;">
        <button type="button" class="btn btn-warning" onClick="cargarCheque()">Guardar</button>
    </div>
</div>  

<input type="hidden" id="h_from_mov" value="<?=$fromMov?>"/>

<div id="d_carga"></div>

<script>
    $(document).ready(function() {
        setChequePersona();
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

    function setChequePersona() {
        $('#s_persona_ch').val(0);
        $('#s_persona_ch').prop('disabled', false);  
        if ($('#s_tipo').val() == 1) {
            $('#s_persona_ch').prop('disabled', true);  
        }
    }    

    function cargarCheque() {
        var goOn =1;
        var mensaje = "";

        if ($('#s_caja_ch').val() == 0) {
            goOn = 0; mensaje = "Seleccione Caja.<br>"; 
        }
        if ($('#s_tipo').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Tipo de Cheque.<br>"; 
        }
        if ($('#s_persona_ch').val() == 0 && $('#s_tipo').val() == 2) {
            goOn = 0; mensaje = mensaje + "Seleccione Persona.<br>"; 
        }
        if ($('#s_banco').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Banco.<br>"; 
        }
        if ($('#s_orden').val() == 0) {
            goOn = 0; mensaje = mensaje + "Indique si el cheque es a la orden o no.<br>"; 
        }
        if ($('#s_formato').val() == 0) {
            goOn = 0; mensaje = mensaje + "Indique si el cheque es físico o electrónico.<br>"; 
        }
        if ($('#s_banco').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Banco.<br>"; 
        }
        if ($('#numero').val() == '0' || $('#numero').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Número de Cheque.<br>"; 
        }
        if ($('#importe_ch').val() == '0' || $('#importe_ch').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese importe.<br>"; 
        }
        if ($('#pago').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese Fecha de Pago.<br>"; 
        }

        if (goOn == 1) {
            Swal.fire({
                title: 'Nuevo Cheque',
                text: '¿Cargar Cheque?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Cargar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("index.php?r=caja/cheque-guarda&caja=" + $('#s_caja_ch').val() +
                    "&banco=" + $('#s_banco').val() +
                    "&tipo=" + $('#s_tipo').val() +"&persona=" + $('#s_persona_ch').val() +
                    "&importe=" + $('#importe_ch').val() + "&numero=" + $('#numero').val() +
                    "&obs=" + $('#obs').val() + "&librador="+ $('#librador').val() +
                    "&pago=" + $('#pago').val() + "&formato=" + $('#s_formato').val() +
                    "&orden=" + $('#s_orden').val()+"&fromMov="+$('#h_from_mov').val()
                    , function (response) {
                        jQuery("#d_carga").html(response);
                    });
                }
            })    
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Cheques',
                html: mensaje
            });
        }    
    }
</script>