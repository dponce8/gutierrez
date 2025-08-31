<script>
    $(document).ready(function() {

        filtrarCheques();

        if (<?=(int)$fromMov?> == 0) {
            $('#chequeModal').modal('hide');
        } else {
            document.getElementById("d_carga_cheque").innerHTML=""; 
        }
    });
</script>  