<?php 
use app\models\PersonaTipo;
use app\models\Sucursal;
?>
    <table class="table table-bordered">
        <thead class="table-dark">
        <tr style="font-size: 10px; background-color:rgb(146, 143, 143); color: #ffffff;">
            <th colspan="5" style="font-size: 10px; font-weight: bold; text-align: center;">Resumen de Estado de cuenta <?=((int)$sucursal == 0 ? ' - Todas las sucursales' : (($sucursalModel = Sucursal::findOne($sucursal)) ? ' - Sucursal: '.$sucursalModel->sucursal : 'Sucursal no encontrada'))?></th>
        </tr>
        <tr style="font-size: 8px;">
            <th style="font-size: 8px; font-weight: bold" width="30px" align="center">#</th>
            <th style="font-size: 8px; font-weight: bold" width="160px" align="left"><?=PersonaTipo::findOne(['id' => $tipo])->tipo?></th>
            <th style="font-size: 8px; font-weight: bold" width="90px" align="right">Debe</th>
            <th style="font-size: 8px; font-weight: bold" width="90px" align="right">Haber</th>
            <th style="font-size: 8px; font-weight: bold" width="90px" align="right">Saldo</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; $totalDebe = 0; $totalHaber = 0; $totalSaldo = 0;
        foreach ($personas as $m) { 
            $totalDebe += $m['compra'];
            $totalHaber += $m['pago'];
            $totalSaldo += $m['saldo'];
            ?>
            <tr style="font-size: 9px;">
                <td width="30px" align="center"><?= $c ?></td>
                <td width="160px" align="left"><?= $m['nombrePersona']?></td>
                <td width="90px" align="right"><?= number_format($m['compra'],2, ',', '.')?></td>
                <td width="90px" align="right"><?= number_format($m['pago'],2, ',', '.')?></td>
                <td width="90px" align="right"><?= number_format($m['saldo'],2, ',', '.')?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
        <tfoot>
        <tr style="font-size: 9px; background-color:rgb(146, 143, 143); color: #ffffff;">
                <td colspan="2" align="right">Total</td>
                <td align="right"><?= number_format($totalDebe,2, ',', '.')?></td>
                <td align="right"><?= number_format($totalHaber,2, ',', '.')?></td>
                <td align="right"><?= number_format($totalSaldo,2, ',', '.')?></td>
            </tr>
        </tfoot>
    </table>