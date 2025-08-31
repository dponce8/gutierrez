<?php 
use app\models\PersonaTipo;
?>
<div style="height : <?php if (count($personas) < 12) {echo (50 + (count($personas) * 30));} else {echo '350';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered" id="tabla_cta_cte">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th></th>
            <th></th>
            <th style="font-size: 10px; font-weight: bold">#</th>
            <th style="font-size: 10px; font-weight: bold"><?=PersonaTipo::findOne(['id' => $tipo])->tipo?></th>
            <th style="font-size: 10px; font-weight: bold">Debe</th>
            <th style="font-size: 10px; font-weight: bold">Haber</th>
            <th style="font-size: 10px; font-weight: bold">Saldo</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; foreach ($personas as $m) { 
            $clase = 'light';
            if ($fila == $c) {$clase = 'success';}
            ?>
            <tr class="table-<?=$clase?>" id="fila_p_<?=$c?>" style="cursor: pointer">
                <td width="30" class="text-center">
                    <a style="color: red; font-size: 14px;" title="Cargar Ajuste" href="javascript:abrirAjuste(<?= $m['id'].','.$c?>)"><i class="fa fa-sliders"></i></a>
                </td>  
                <td width="30" class="text-center">
                    <a style="color: blue; font-size: 14px;" title="Imprimir Cuenta" href="javascript:imprimirCuenta(<?= $m['id']?>)"><i class="fa fa-print"></i></a>
                </td>  
                <td onClick="mostrarDetalle(<?=$m['id'].','.$c?>)"><?= $c ?></td>
                <td onClick="mostrarDetalle(<?=$m['id'].','.$c?>)"><?= $m['nombrePersona']?></td>
                <td onClick="mostrarDetalle(<?=$m['id'].','.$c?>)"><?= number_format($m['compra'],2, ',', '.')?></td>
                <td onClick="mostrarDetalle(<?=$m['id'].','.$c?>)"><?= number_format($m['pago'],2, ',', '.')?></td>
                <td onClick="mostrarDetalle(<?=$m['id'].','.$c?>)"><?= number_format($m['saldo'],2, ',', '.')?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
    </table>
</div>

<input id="h_fila_per" type="hidden" value="<?=$c?>"/>

<script>
    function imprimirCuenta(id) {
        window.open("index.php?r=site/cuenta-imprime&id=" + id + "&sucursal=" + $('#s_sucursal').val());
    }
</script>    