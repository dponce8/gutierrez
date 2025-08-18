<?php 
use edwinhaq\simpleloading\SimpleLoading;
SimpleLoading::widget();
?>
<div class="row" style="padding-top: 10px; padding-left: 15px; padding-bottom: 15px;">
    <div class="col-sm-3">
      <label class="sr-only" for="inlineFormInputName">Nueva contraseña</label>
      <input type="password" class="form-control" id="pass1" placeholder="Ingrese Nueva Contraseña" >
    </div>
    <div class="col-sm-3">
      <label class="sr-only" for="inlineFormInputName">Repetir Contraseña</label>
      <input type="password" class="form-control" id="pass2" placeholder="Repetir Contraseña" >
    </div>
    <div class="col-sm-1">
        <button type="button" onClick="cambiarPass()" class="btn btn-success">Cambiar Contraseña</button>
    </div>   
</div>  

<div id="d_recuperar" class="col-sm-12"></div>

<script>
    $(document).ready(function() {
    });

    function cambiarPass() {
        $.ajax({
            url: 'site/index', statusCode: { '302': function() { window.open("site/index", "_self"); } },
            success: function() {
                if ($('#pass1').val() != '' && $('#pass2').val() != '' && ($('#pass1').val() == $('#pass2').val())) {
                    SimpleLoading.start('gears');
                    $.post("index.php?r=site/cambio-pass-accion&pass1="+ $('#pass1').val()
                        , function (response) {
                            jQuery("#d_recuperar").html(response);
                        });
                }   else {
                    alert("Las nuevas contraseñas ingresadas deben coincidir.");
                } 
            }
        });
            
    }
</script>
