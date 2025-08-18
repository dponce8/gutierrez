
<?php if ($salida == 1) { ?>    
    <div class="row" style="padding: 10px">    
        <div class="col-md-12 alert alert-success" role="alert" style="padding-left: 10px;">
        La contraseña se cambió exitosamente.
        </div>
    </div>
<?php } else { ?>   
    <div class="col-md-12 alert alert-danger text-center" role="alert">
    </div>
<?php } ?>   


<script>
    $(document).ready(function() {
        SimpleLoading.stop();
    });
</script>   