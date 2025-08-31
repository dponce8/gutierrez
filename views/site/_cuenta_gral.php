<?php
use yii\helpers\Html;
use app\controllers\SiteController;
use app\models\Sueldosempresas;
$total= 0;

$tbl =
    '<table border="0" cellpadding="3">
        <tr>
            <td rowspan="3" width="160" style="font-size: 9px;">' . Html::img('@web/images/logo.png', ['width' => '160px']) . '</td>
            <td align="left" width="280" style="font-size: 12px; font-weight: bolder;">Resumen de Cuenta '.((int)$sucursal == 0 ? ' - Todas las empresas' : (($sucursalModel = Sueldosempresas::findOne($sucursal)) ? ' - '.$sucursalModel->Empresa : 'Empresa no encontrada')).'</td>
            <td align="left" width="100" style="font-size: 9px;  ">'.date("d/m/Y H:i:s").'</td>
        </tr>
        <tr>
            <td align="left" colspan="3" style="font-size: 12px;  font-weight: bolder; ">'.$tipoPersona.'</td>
        </tr>
        <tr>
            <td align="left" width="50" style="font-size: 1px;"></td>
        </tr>        
        <tr>
            <td align="left" style="font-size: 11px;">';
                $tbl = $tbl.'
                <table border="0" cellpadding="0">
                    <tr style="font-weight: bolder">
                        <td align="left" width="230">Persona</td>
                        <td align="right" width="100">Compras</td>
                        <td align="right" width="100">Pagos</td>
                        <td align="right" width="100">Saldo</td>
                    </tr>';
                    $compras = 0; $pagos = 0; $saldo = 0;
                foreach ($personas as $g) {
                    $tbl = $tbl .'
                    <tr style="background-color:rgb(236, 229, 229); font-weight: bold;">
                        <td align="left" width="230" style="">'.$g['nombrePersona'].'</td>
                        <td align="right" width="100">'.number_format(floatval($g['compra']), 2, ",", ".").'</td>
                        <td align="right" width="100">'.number_format(floatval($g['pago']), 2, ",", ".").'</td>
                        <td align="right" width="100">'.number_format(floatval($g['saldo']), 2, ",", ".").'</td>
                    </tr>';
                    $compras = $compras + floatval($g['compra']);
                    $pagos = $pagos + floatval($g['pago']);
                    $saldo = $saldo + floatval($g['saldo']);
                    $items = SiteController::getItemsCuenta($g['id'], $sucursal);
                    $tbl = $tbl .'
                    <tr style="font-size: 4px;">
                        <th width="60" align="center"></th>
                    </tr>
                    <tr style="font-size: 8px;">
                        <th width="100" align="center">Fecha / Hora</th>
                        <th width="100" align="center">Operación</th>
                        <th width="80" align="right">Compra</th>
                        <th width="80" align="right">Pago</th>
                    </tr>';
                    $debe = 0; $haber = 0;
                    foreach ($items as $m) {
                        $debe = $debe + floatval($m['debe']);
                        $haber = $haber + floatval($m['haber']);
                        $tbl = $tbl .'
                        <tr style="font-size: 8px;">
                            <th align="center">'.date("d/m/Y",strtotime($m['fecha'])).' '.$m['hora'].'</th>
                            <th align="center">'.$m['tipoMovimiento'].'</th>
                            <th align="right">'.number_format(floatval($m['debe']), 2, ",", ".").'</th>
                            <th align="right">'.number_format(floatval($m['haber']), 2, ",", ".").'</th>
                        </tr>';
                    }
                    $tbl = $tbl .'                   
                    <tr style="font-size: 4px;">
                        <th width="60" align="center"></th>
                    </tr>';
                }   
                $tbl = $tbl .'
                    <tr style="background-color:rgb(12, 12, 12); font-weight: bold; color: white">
                        <td align="left" width="230" style="">Total General</td>
                        <td align="right" width="100">'.number_format(floatval($compras), 2, ",", ".").'</td>
                        <td align="right" width="100">'.number_format(floatval($pagos), 2, ",", ".").'</td>
                        <td align="right" width="100">'.number_format(floatval($saldo), 2, ",", ".").'</td>
                    </tr>';             
                $tbl = $tbl.'</table>';   
                
    $tbl = $tbl.'</td>
    </tr></table>';
echo $tbl; ?>