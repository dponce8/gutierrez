<div style="height : <?php if (count($listado) < 3) {echo (45 + (count($listado) * 35));} else {echo '130';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">Medio</th>
            <th style="font-size: 10px; font-weight: bold">Importe</th>
            <th style="font-size: 10px; font-weight: bold">Detalle</th>
            <th style="font-size: 10px; font-weight: bold"></th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; $total = 0; foreach ($listado as $m) { 
            $total = $total + $m['importe'];
            ?>
            <tr class="table-light">
                <td><?= $m['medio']?></td>
                <td><?= number_format(floatval($m['importe']), 2, ',', '.')?></td>
                <td>
                    <?php
                    if ($m['id_medio'] == 2 or $m['id_medio'] == 3) {
                        echo $m['tarjeta'];
                    }
                    if ($m['id_medio'] == 4) {
                        echo $m['cuenta'];
                    }
                    if ($m['id_medio'] == 5) {
                        echo 'N°: '.$m['nro_cheque'];
                    }
                    ?>
                </td>
                <td>
                    <a style="color: red; font-size: 13px;" title="Quitar" href="javascript:eliminarMedio(<?= $m['id']?>)"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#total_operacion').val("<?=number_format(floatval($total), 2, ',', '.')?>");
        
        $('#importe').val("");
        $('#s_cheque').val(0);
        $('#s_credito').val(0);
        $('#s_debito').val(0);
        $('#s_cuenta').val(0);
        $('#s_medio').val(0);
        filtrarMediosCarga();
    });

    function eliminarMedio(id) {
        $.post("index.php?r=caja/movimiento-medio&eliminar=1"+"&id="+id
            , function (response) {
                jQuery("#d_mov_medio").html(response);
            });
    }
</script>
