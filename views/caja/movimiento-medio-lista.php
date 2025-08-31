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

<?php if ($facturas != null) { ?>
    <div style="padding-top: 20px; height : <?php if (count($facturas) < 3) {echo (60 + (count($facturas) * 35));} else {echo '130';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered">
        <thead class="table-primary">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">Pto Vta</th>
            <th style="font-size: 10px; font-weight: bold">Tipo</th>
            <th style="font-size: 10px; font-weight: bold">Número</th>
            <th style="font-size: 10px; font-weight: bold">Fecha Factura</th>
            <th style="font-size: 10px; font-weight: bold">Importe</th>
            <th style="font-size: 10px; font-weight: bold">Observaciones</th>
            <th style="font-size: 10px; font-weight: bold">Usuario</th>
            <th style="font-size: 10px; font-weight: bold">Creado</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; foreach ($facturas as $m) { ?>
            <tr class="table-light" >
                <td><?= $m['id_punto']?></td>
                <td><?= $m['tipo']?></td>
                <td><?= $m['numero']?></td>
                <td><?= date("d/m/Y",strtotime($m['fecha']))?></td>
                <td><?= number_format($m['importe'], 2, ",", ".")?></td>
                <td><?= $m['obs']?></td>
                <td><?= $m['usuario']?></td>
                <td><?= date("d/m/Y H:i",strtotime($m['creado']))?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
    </table>
</div>
<?php } else { ?>
    <div class="alert alert-warning">No hay facturas asociadas</div>
<?php } ?>
