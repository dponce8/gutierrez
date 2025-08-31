<script>
$(document).ready(function() {
    $('#movModal').modal('hide');
    filtrarMovimientos();  
    <?php if ($salida == 1 and (int)$idMov > 0 and $concepto != 5) { ?>  
        window.open("index.php?r=caja/movimiento-imprime&id=" + <?=(int)$idMov?>);                    
    <?php } ?>   
}); 
</script>    