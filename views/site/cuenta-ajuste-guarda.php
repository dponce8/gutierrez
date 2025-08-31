<?php if ($salida == 1) { ?>
    <script>
        verPersonas($('#h_tipo_persona').val(), $('#h_fila_persona').val());
        mostrarDetalle($('#h_id_persona').val(), $('#h_fila_persona').val());
        $('#ajusteModal').modal('hide');
    </script>    
<?php } ?>