<script>
    $(document).ready(function() {
        $('#info_cantidad').val('<?=(int)$total['cantidad']?>');
        $('#info_importe').val('<?=number_format(floatval($total['importe']), 2, ",", ".")?>');
    });
</script>