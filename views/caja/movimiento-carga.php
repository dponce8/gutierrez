<div class="row">
    <div class="form-group col-sm-6">
        <div class="row" style="padding-left: 5px; padding-right: 5px;">
            <div class="form-group col-sm-6" style="margin-top: -10px;">
                <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Empresa</label>
                <select class="form-control" onChange="mostrarViajes()" id="s_caja" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
                    <option value="0">Empresas ...</option>
                    <?php foreach ($cajas as $s) {?>
                        <option value="<?=$s['idEmpresa']?>"><?=$s['Empresa']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-sm-6" style="margin-top: -10px;">
                <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Concepto</label>
                <select class="form-control" onChange="mostrarPersonas()" id="s_concepto" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
                    <option value="0">Conceptos ...</option>
                    <?php foreach ($conceptos as $s) {?>
                        <option value="<?=$s['id']?>"><?=$s['concepto']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-sm-12" id="d_persona_mov" style="margin-top: -10px;">
                
            </div>
        </div>   
        <div class="row" style="padding-left: 5px; padding-right: 5px;">   
            <div class="form-group col-sm-6" style="margin-top: -10px;">
                <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Tipo Comp.</label>
                <select class="form-control" id="s_factura" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
                    <option value="0">Tipo ...</option>
                    <?php foreach ($facturas as $s) {?>
                        <option value="<?=$s['id']?>"><?=$s['tipo']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-sm-6" style="margin-top: -10px; ">
                <label style="font-size: 11px; ">N° Comp.</label>
                <input id="nro_factura" type="text" class="form-control ">
            </div>
        </div>   
        <div class="row" style="padding-left: 5px; padding-right: 5px;">            
            <div class="form-group col-sm-12" style="margin-top: -10px; ">
                <label style="font-size: 11px; ">Observaciones</label>
                <input id="obs" type="text" class="form-control ">
            </div>
        </div>   
        <div class="row" style="padding-left: 5px; padding-right: 5px;">  
            <div class="form-group col-sm-12 text-right" style="margin-top: -10px; ">
                <label style="font-size: 11px;">Total Operación</label>
                <input id="total_operacion" style="font-size: 14px; font-weight: bold; direction: rtl; text-align: right; padding-right: 2px; padding-left: 2px;" disabled type="text" class="form-control ">
            </div>
        </div>   
    </div>    
    <div class="form-group col-sm-6">
        <div class="row" style="padding-left: 5px; padding-right: 5px;">
            <div class="form-group col-sm-5" style="margin-top: -10px;">
                <label style="margin-bottom: 0px; font-size: 11px;" for="numero" id="lab_2">Seleccione Medio</label>
                <select class="form-control" onChange="filtrarMediosCarga()" id="s_medio" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
                    <option value="0">Medios ...</option>
                    <?php foreach ($medios as $t) {?>
                        <option value="<?=$t['id']?>"><?=$t['medio']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-sm-5" style="margin-top: -10px; ">
                <label style="font-size: 11px; ">Importe</label>
                <input id="importe" type="text" class="form-control input_numero">
            </div>
            <div class="form-group col-sm-2" style="margin-top: 5px; ">
                <a style="color: red; font-size: 25px;" title="Agrega Medio" href="javascript:agregarMedio()"><i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>   
        <div class="row" style="padding-left: 5px; padding-right: 5px;">
            <div class="form-group col-sm-10" id="d_cheques" style="margin-top: -10px; display: none">
                
            </div>
            <div class="form-group col-sm-2" id="d_cheques2" style="padding-top:15px; display: none">
                <a style="color: green; font-size: 17px;" title="Nuevo Cheque" href="javascript:mostrarCargaCheque()"><i class="fa fa-plus"></i></a>
            </div>

            <div class="form-group col-sm-12" id="d_cuentas" style="margin-top: -10px; display: none">
                <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Cuenta</label>
                <select class="form-control" id="s_cuenta" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
                    <option value="0">Cuentas ...</option>
                    <?php foreach ($cuentas as $t) {?>
                        <option value="<?=$t['id']?>"><?=$t['cuenta']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-sm-12" id="d_creditos" style="margin-top: -10px; display: none">
                <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Tarjeta</label>
                <select class="form-control" id="s_credito" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
                    <option value="0">Tarjetas ...</option>
                    <?php foreach ($creditos as $t) {?>
                        <option value="<?=$t['id']?>"><?=$t['tarjeta']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-sm-12" id="d_debitos" style="margin-top: -10px; display: none">
                <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Tarjeta</label>
                <select class="form-control" id="s_debito" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
                    <option value="0">Tarjetas ...</option>
                    <?php foreach ($debitos as $t) {?>
                        <option value="<?=$t['id']?>"><?=$t['tarjeta']?></option>
                    <?php } ?>
                </select>
            </div>
        </div> 
        <div class="row" style="padding-left: 5px; padding-right: 5px; padding-top: 17px">
            <div class="form-group col-sm-12" id="d_mov_medio" style="margin-top: -10px;">
            </div>
        </div>
        
    </div>    
</div>
<div class="row">
    <div class="form-group col-sm-12" id="d_carga_cheque" style="margin-top: -10px;">
    </div>
    <div class="form-group col-sm-12" id="d_lista_factura" style="margin-top: -10px;">
    </div>
    <div class="form-group col-sm-12" id="d_seleccion_factura" style="margin-top: -10px;">
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#d_info_factura').hide();
        $.post("index.php?r=caja/movimiento-medio", function (response) {
            jQuery("#d_mov_medio").html(response);
        });
        mostrarPersonas();
    });

    function mostrarViajes() {
        $('#d_lista_factura').show();
        $.post("index.php?r=caja/viaje-lista&idPersona=" + $('#s_persona').val() + "&empresa=" + $('#s_caja').val(), function (response) {
            jQuery("#d_lista_factura").html(response);
        });
    }

    function seleccionarFactura(id) {
        let valor = 0;
        if ($('#ch_factura_'+id).is(':checked')) {
            valor = 1;
        } 
        $('#d_info_factura').show();
        $.post("index.php?r=caja/movimiento-carga-factura&id="+id+"&valor="+valor+"&carga=1", function (response) {
            jQuery("#d_seleccion_factura").html(response);
        });
    }

    function mostrarCargaCheque() {
        $.post("index.php?r=caja/cheque-carga&fromMov=1" , function (response) {
            jQuery("#d_carga_cheque").html(response);
        });
    } 

    function mostrarPersonas() {       
        $('#d_info_factura').hide();
        $('#d_lista_factura').hide();
        $.post("index.php?r=caja/movimiento-persona&concepto="+$('#s_concepto').val() , function (response) {
            jQuery("#d_persona_mov").html(response);
        });

        $('#s_medio').val(0);
        filtrarMediosCarga();
    } 

    $(".input_numero").on({
        "focus": function (event) {
            $(event.target).select();
        },
        "keyup": function (event) {
            $(event.target).val(function (index, value ) {
                //return value.replace(/[^0-9.\-]/g, "");
                return value.replace(/[^0-9.,\-]/g, "");
            });
        }
    });

    function agregarMedio() {
        var goOn =1;
        var mensaje = "";

        if ($('#s_medio').val() == 0) {
            goOn = 0;
            mensaje = "Seleccione Medio de pago.<br>";
        } else {
            if ($('#s_medio').val() == 5 && $('#s_cheque').val() == 0) {
                goOn = 0;
                mensaje = mensaje + "Seleccione Cheque.<br>";
            } else {
                if ($('#s_medio').val() == 2 && $('#s_credito').val() == 0) {
                    goOn = 0;
                    mensaje = mensaje + "Seleccione Tarjeta.<br>";
                } else {
                    if ($('#s_medio').val() == 3 && $('#s_debito').val() == 0) {
                        goOn = 0;
                        mensaje = mensaje + "Seleccione Tarjeta.<br>";
                    } else {
                        if ($('#s_medio').val() == 4 && $('#s_cuenta').val() == 0) {
                            goOn = 0;
                            mensaje = mensaje + "Seleccione Cuenta.<br>";
                        }
                    }
                }
            }
        }

        if ($('#importe').val() == '' || $('#importe').val() == '0') {
            goOn = 0;
            mensaje = mensaje + "Ingrese Importe.<br>";
        }   

        if (goOn == 1) {
            $.post("index.php?r=caja/movimiento-medio&medio=" + $('#s_medio').val() +
            "&importe=" + $('#importe').val() +"&cheque=" + $('#s_cheque').val() +
            "&cuenta=" + $('#s_cuenta').val() + "&credito=" + $('#s_credito').val() +
            "&debito=" + $('#s_debito').val() + "&guarda=1" 
            , function (response) {
                jQuery("#d_mov_medio").html(response);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Medios',
                html: mensaje
            });
        }    
    }    

    function filtrarMediosCarga() {
        $('#importe').prop('disabled', false);
        document.getElementById("d_cheques").style.display = "none";
        document.getElementById("d_cheques2").style.display = "none";
        document.getElementById("d_cuentas").style.display = "none";
        document.getElementById("d_creditos").style.display = "none";
        document.getElementById("d_debitos").style.display = "none";
        document.getElementById("d_carga_cheque").innerHTML=""; 

        if ($('#s_medio').val() == 5 && $('#s_concepto').val() != 0) {
            $('#importe').prop('disabled', true);
            document.getElementById("d_cheques").style.display = "block";
            document.getElementById("d_cheques2").style.display = "block";
            filtrarCheques();
        }
        if ($('#s_medio').val() == 2) {
            document.getElementById("d_creditos").style.display = "block";
        }
        if ($('#s_medio').val() == 3) {
            document.getElementById("d_debitos").style.display = "block";
        }
        if ($('#s_medio').val() == 4) {
            document.getElementById("d_cuentas").style.display = "block";
        }
    }

    function filtrarCheques() {
        $.post("index.php?r=caja/movimiento-cheque&concepto=" + $('#s_concepto').val()
        , function (response) {
            jQuery("#d_cheques").html(response);
        });
    }

    function cargarMovimiento() {
        var goOn =1;
        var mensaje = ""; 

        if ($('#s_caja').val() == 0) {
            goOn = 0; mensaje = "Seleccione Caja.<br>"; 
        }
        if ($('#s_concepto').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Concepto.<br>"; 
        }
        if ($('#s_concepto').val() == 4 && $('#nro_recibo').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese N° Recibo.<br>"; 
        }
        if ($('#s_persona').val() == 0 && $('#s_concepto').val() != 5 && persona == 1) {
            goOn = 0; mensaje = mensaje + "Seleccione Persona.<br>"; 
        }
        if ($('#total_operacion').val() == '0,00' || $('#total_operacion').val() == '0,0' || $('#total_operacion').val() == '') {
            goOn = 0; mensaje = mensaje + "La operación no tiene un importe.<br>"; 
        }
        if ($('#s_factura').val() == 0) {
            goOn = 0; mensaje = mensaje + "Seleccione Tipo de Factura.<br>"; 
        }
        if ($('#nro_factura').val() == '') {
            goOn = 0; mensaje = mensaje + "Ingrese N° Factura.<br>"; 
        }

        if (goOn == 1) {
            Swal.fire({
                title: 'Ingreso / Egreso',
                text: '¿Cargar Movimiento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Cargar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("index.php?r=caja/movimiento-guarda&caja=" + $('#s_caja').val() +
                    "&concepto=" + $('#s_concepto').val() +"&persona=" + $('#s_persona').val() +
                    "&importe=" + $('#total_operacion').val().replace(/\./g, '').replace(/,/g, '.') + 
                    "&obs=" + $('#obs').val()  +
                    "&factura=" + $('#s_factura').val() + "&nro_factura=" + $('#nro_factura').val()
                    + "&id_viaje=" + $('#s_viaje').val()
                    , function (response) {
                        jQuery("#d_mov_lista").html(response);
                    });
                    
                }
            })    
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ingreso / Egreso',
                html: mensaje
            });
        }    
    }
</script>