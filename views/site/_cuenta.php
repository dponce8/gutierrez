<?php
use yii\helpers\Html;
$total= 0;

$tbl =
    '<table border="0" cellpadding="3">
        <tr>
            <td rowspan="3" width="160" style="font-size: 9px;">' . Html::img('@web/images/logo.png', ['width' => '160px']) . '</td>
            <td align="left" width="250" style="font-size: 12px; font-weight: bolder;">Resumen de Cuenta</td>
            <td align="left" width="120" style="font-size: 9px;  ">'.date("d/m/Y H:i:s").'</td>
        </tr>
        <tr>
            <td align="left" colspan="3" style="font-size: 12px;  font-weight: bolder; ">'.$datos['nombrePersona'].'</td>
        </tr>
        <tr>
            <td align="left" width="50" style="font-size: 1px;"></td>
        </tr>        
        <tr>
            <td align="left" style="font-size: 11px;">';
            if ($items != null) {
                $tbl = $tbl.'
                <table border="0" cellpadding="1">
                    <tr style="font-weight: bolder">
                        <td align="center" width="60" style="font-size: 8px; border-bottom-style: solid;">Fecha</td>
                        <td align="left" width="80" style="font-size: 8px; border-bottom-style: solid;">Operación</td>
                        <td align="right" width="60" style="font-size: 8px; border-bottom-style: solid;">Debe</td>
                        <td align="right" width="60" style="font-size: 8px; border-bottom-style: solid;">Haber</td>
                        <td align="left" width="200" style="font-size: 8px; border-bottom-style: solid;">Observaciones</td>
                    </tr>';
                    $debe = 0; $haber = 0;
                foreach ($items as $g) {
                    $debe = $debe + floatval($g['debe']);
                    $haber = $haber + floatval($g['haber']);
                $tbl = $tbl .'
                    <tr>
                        <td align="center" width="60" style="font-size: 9px;">'.date("d/m/Y",strtotime($g['fecha'])).'</td>
                        <td align="left" width="80" style="font-size: 9px;">'.$g['tipoMovimiento'].'</td>
                        <td align="right" width="60" style="font-size: 9px;">'.number_format(floatval($g['debe']), 2, ",", ".").'</td>
                        <td align="right" width="60" style="font-size: 9px;">'.number_format(floatval($g['haber']), 2, ",", ".").'</td>
                        <td align="left" width="200" style="font-size: 9px;">'.$g['obs'].'</td>
                    </tr>';
                }  
                $tbl = $tbl .'
                    <tr>
                        <td align="center" width="60" style="font-size: 9px; border-top-style: solid;"></td>
                        <td align="left" width="80" style="font-size: 9px; border-top-style: solid;"></td>
                        <td align="right" width="60" style="font-size: 9px; border-top-style: solid; font-weight: bolder">'.number_format(floatval($debe), 2, ",", ".").'</td>
                        <td align="right" width="60" style="font-size: 9px; border-top-style: solid; font-weight: bolder">'.number_format(floatval($haber), 2, ",", ".").'</td>
                        <td align="left" width="200" style="font-size: 10px; border-top-style: solid; font-weight: bolder">Saldo: '.number_format(floatval($debe) - floatval($haber), 2, ",", ".").'</td>
                    </tr>'; 

                $tbl = $tbl.'</table>';                 
            }
    $tbl = $tbl.'</td>
    </tr></table>';
echo $tbl; ?>