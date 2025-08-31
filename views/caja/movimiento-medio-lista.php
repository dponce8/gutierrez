<div style="height : <?php if (count($listado) < 3) {echo (60 + (count($listado) * 35));} else {echo '130';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
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
        <?php foreach ($listado as $m) { ?>
            <tr class="table-light">
                <td><?= $m['medio']?></td>
                <td><?= number_format(floatval($m['importe']), 2, ",", ".")?></td>
                <td>
                    <?php
                    if ($m['id_medio'] == 2 or $m['id_medio'] == 3) {
                        echo $m['tarjeta'];
                    }
                    if ($m['id_medio'] == 4) {
                        echo $m['cuenta'].' Banco: '.$m['banco_cta'];
                    }
                    if ($m['id_medio'] == 5) {
                        echo 'N°: '.$m['nro_cheque'].' Banco: '.$m['banco'];
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>    
        </tbody>
    </table>
</div>


