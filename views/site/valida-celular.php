<?php 
use edwinhaq\simpleloading\SimpleLoading;
SimpleLoading::widget();
?>
<div class="row" style="padding-top: 10px; padding-left: 15px; padding-bottom: 15px;">
    <div class="col-sm-3">
      <label  for="inlineFormInputName">N° Celular (sin 0 y sin 15)</label>
      <input type="text" class="form-control" id="cel" placeholder="Por ejemplo: 3865654321" >
    </div>
    <div class="col-sm-4" style="padding-top: 24px;">
        <button type="button" onClick="validarCelular()" class="btn btn-success">Validar N° Celular</button>
    </div>   
</div>   

<input type="hidden" id="h_id" value="<?=$id?>">

<div id="d_validar_cel" class="col-sm-12"></div>

<script>
    $(document).ready(function() {
    });

    function validarCelular() {
        $.ajax({
            url: 'site/index', statusCode: { '302': function() { window.open("site/index", "_self"); } },
            success: function() {
                SimpleLoading.start('gears');
                $.post("index.php?r=site/valida-celular-mensaje&cel="+ $('#cel').val() + "&id=" + $('#h_id').val()
                    , function (response) {
                        jQuery("#d_validar_cel").html(response);
                    });
            }
        });
        
    }
</script>