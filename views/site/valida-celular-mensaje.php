<?php if ($salida == 1) { ?>
    <div class="row" style="padding: 30px">
        <div class="col-sm-12 text-center card-header alert-success" style="font-weight: bolder">
            <h4>Se ha enviado un mensaje de WhatsApp al número ingresado, por favor siga los pasos indicados para finalizar la validación</h4>
        </div>    
    </div>
<?php } else {?>
    <div class="row" style="padding: 30px">
        <div class="col-sm-12 text-center card-header alert-danger" style="font-weight: bolder">
            <h4>Se ha producido un error en el envío, por favor intente nuevamente</h4>
        </div>    
    </div>
<?php } ?>

<script>
    $(document).ready(function() {
        SimpleLoading.stop();
    });
</script>