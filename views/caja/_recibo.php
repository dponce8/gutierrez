<?php
use yii\helpers\Html;
$total= 0;
if ($tipoConcepto == 2) {    // Orden de pago
    $tbl =
    '<table border="0" cellpadding="5">
        <tr>
            <td rowspan="3" width="180" style="font-size: 9px; border-top-style: solid; border-left-style: solid;" align="center">
            <div style="padding-top: 80px;">
                ' . Html::img('@web/images/logo.png', ['width' => '180px']) . '
            </div>
            </td>
            <td rowspan="3" width="10" style="font-size: 9px; border-top-style: solid;"></td>
            <td align="left" width="150" style="font-size: 14px; font-weight: bolder; border-top-style: solid;"></td>
            <td align="right" width="200" style="font-size: 9px; border-top-style: solid;  border-right-style: solid;">'.date("d/m/Y H:i:s").'</td>
        </tr>
        <tr>
            <td align="left" colspan="3" style="font-size: 10px; border-right-style: solid;"></td>
        </tr>
        <tr>
            <td align="left" colspan="2" width="180"  style="font-size: 10px;; "></td>
            <td align="right" width="170" style="font-size: 10px; border-right-style: solid; "><b>ORDEN DE PAGO N° '.$datos['nro_comprobante'].'</b></td>
        </tr>     
        <tr>
            <td align="left" width="60" style="font-size: 9px; border-left-style: solid; solid; border-top-style: solid;">
                A favor de:
            </td>
            <td align="left" width="210" style="font-size: 9px; solid; border-top-style: solid;">
                <b>'.$datos['nombrePersona'].'</b>
            </td>
            <td align="left" width="60" style="font-size: 9px; border-top-style: solid;">
                Domicilio:
            </td>
            <td align="left" width="210" style="font-size: 9px; border-right-style: solid; border-top-style: solid;">
                <b>'.$datos['domicilio'].'</b>
            </td>
        </tr>        
        <tr>
            <td align="left" width="60" style="font-size: 9px; border-left-style: solid; solid; border-bottom-style: solid;">
                CUIT:
            </td>
            <td align="left" width="210" style="font-size: 9px; solid; border-bottom-style: solid;">
                <b>'.$datos['cuit'].'</b>
            </td>
            <td align="left" width="60" style="font-size: 9px; border-bottom-style: solid;">
                Localidad:
            </td>
            <td align="left" width="210" style="font-size: 9px; border-right-style: solid; border-bottom-style: solid;">
                <b>'.$datos['localidad'].'</b>
            </td>
        </tr>   
        <tr>
            <td width="540" style="font-size: 9px; border-right-style: solid; border-left-style: solid; solid; ">
                Por los siguientes conceptos:
            </td>
        </tr>        
        <tr>
            <td width="400" style="font-size: 9px; border-left-style: solid; solid;">
                Concepto
            </td>
            <td width="140" align="right" style="font-size: 9px; border-right-style: solid; solid;">
                Importe
            </td>
        </tr>        
        <tr>
            <td width="400" style="font-size: 9px; border-left-style: solid; solid;">
                <b>'.$datos['concepto'].'</b>
            </td>
            <td width="140" align="right" style="font-size: 9px; border-right-style: solid; solid;">
            <b>'.number_format($datos['importe'], 2, ",", ".").'</b>
            </td>
        </tr>   
        <tr>
            <td width="540" height="150" style="font-size: 9px; border-left-style: solid; solid; border-right-style: solid; solid; border-bottom-style: solid; ">
                
            </td>
        </tr>       
        <tr>
            <td width="540" style="font-size: 9px; border-left-style: solid; solid; border-right-style: solid; solid; ">
                Medios de pago
            </td>
        </tr>       
        <tr>
            <td align="left" height="150" style="font-size: 11px; border-right-style: solid; border-left-style: solid; border-bottom-style: solid;">';
            if ($medios != null) {
                $tbl = $tbl.'
                <table border="0" cellpadding="1">
                    <tr style="font-weight: bolder">
                        <td align="left" width="90" style="font-size: 8px; ">Medio</td>
                        <td align="right" width="90" style="font-size: 8px; ">Importe</td>
                        <td align="right" width="20" style="font-size: 8px; "></td>
                        <td align="left" width="300" style="font-size: 8px; ">Detalle</td>
                    </tr>';
                    
                foreach ($medios as $g) {
                    $detalle = '';
                    /*if ($g['id_medio'] == 2 or $g['id_medio'] == 3) {
                        $detalle = 'Tarjeta: '.$g['tarjeta'];
                    }
                    if ($g['id_medio'] == 4) {
                        $detalle =  'Cuenta: '.$g['cuenta'].' Banco: '.$g['banco_cta'];
                    }
                    if ($g['id_medio'] == 5) {
                        $detalle =  '['.$g['tipo'].'] '.' N°: '.$g['nro_cheque'].' Banco: '.$g['banco'];
                    }*/
                $tbl = $tbl .'
                    <tr>
                        <td align="left" width="90" style="font-size: 9px;">'.$g['medio'].'</td>
                        <td align="right" width="90" style="font-size: 9px;">'.number_format(floatval($g['importe']), 2, ",", ".").'</td>
                        <td align="right" width="20" style="font-size: 9px;"></td>
                        <td align="left" width="300" style="font-size: 9px;">'.$detalle.'</td>
                    </tr>';
                }  

                $tbl = $tbl.'</table></td></tr>';                 
            }
        $tbl = $tbl.'
        <tr>
            <td width="540" style="font-size: 9px; border-left-style: solid; border-right-style: solid;">
                Son Pesos: <b>'.\app\controllers\CajaController::numeroToLetra($datos['importe']).'</b>
            </td>
        </tr>           
        <tr>
            <td width="540" style="font-size: 10px; border-left-style: solid; border-right-style: solid;">
                Observaciones: <b>'.$datos['obs'].'</b>
            </td>
        </tr>  
        <tr>
            <td width="400" align="right" style="font-size: 9px; border-left-style: solid; border-top-style: solid;">
                Total: 
            </td>
            <td width="140" style="font-size: 12px; border-right-style: solid; border-top-style: solid;">
                <b>'.number_format($datos['importe'], 2, ",", ".").'</b>
            </td>
        </tr>          
        <tr>
            <td width="180" height="70" align="left" style="font-size: 9px; border-left-style: solid; border-top-style: solid;  border-bottom-style: solid;">
                Confeccionado
            </td>
            <td width="180" height="70" align="left" style="font-size: 9px; border-left-style: solid; border-top-style: solid;  border-bottom-style: solid;">
                Control
            </td>
            <td width="180" height="70" align="left" style="font-size: 9px; border-right-style: solid; border-left-style: solid; border-top-style: solid;  border-bottom-style: solid;">
                Autoriza
            </td>
        </tr>          
        <tr>
            <td width="180" height="60" align="left" style="font-size: 9px; border-left-style: solid; border-top-style: solid;">
                
            </td>
            <td width="180" height="60" align="left" style="font-size: 9px; border-left-style: solid; border-top-style: solid;">
                
            </td>
            <td width="180" height="60" align="left" style="font-size: 9px; border-right-style: solid; border-left-style: solid; border-top-style: solid;">
                
            </td>
        </tr>          
        <tr>
            <td width="180" align="center" style="font-size: 8px; border-left-style: solid; border-bottom-style: solid;">
                ___________________________<br>Firma Beneficiario
            </td>
            <td width="180" align="center" style="font-size: 8px; border-left-style: solid; border-bottom-style: solid;">
                ___________________________<br>Aclaración
            </td>
            <td width="180" align="center" style="font-size: 8px; border-right-style: solid; border-left-style: solid; border-bottom-style: solid;">
                ___________________________<br>N°: DNI
            </td>
        </tr>     
        <tr>
            <td width="540" style="font-size: 8px; ">
                Usuario: '.$datos['usuario'].'
            </td>
        </tr>       
        ';
    $tbl = $tbl.'</table>';
    echo $tbl; 
}  else {   // Recibo
    $tbl =
    '<table border="0" cellpadding="5">
        <tr>
            <td rowspan="3" width="180" style="font-size: 9px; border-top-style: solid; border-left-style: solid;" align="center">
            <div style="padding-top: 80px;">
                ' . Html::img('@web/images/logo.png', ['width' => '180px']) . '
            </div>
            </td>
            <td rowspan="3" width="10" style="font-size: 9px; border-top-style: solid;"></td>
            <td align="left" width="180" style="font-size: 14px; font-weight: bolder; border-top-style: solid;"></td>
            <td align="right" width="170" style="font-size: 9px; border-top-style: solid;  border-right-style: solid;">'.date("d/m/Y H:i:s").'</td>
        </tr>
        <tr>
            <td align="left" width="80" colspan="3" style="font-size: 10px; border-right-style: solid;"></td>
            <td align="center" width="30" colspan="3" style="font-size: 17px; border-bottom-style: solid; border-top-style: solid;  border-right-style: solid;"> X</td>
            <td align="left" width="240" colspan="3" style="font-size: 10px; border-right-style: solid;"></td>
        </tr>
        <tr>
            <td align="left" colspan="2" width="78"  style="font-size: 10px;; "></td>
            <td align="center" colspan="2" width="40"  style="font-size: 5px;; ">Documento no válido como factura</td>
            <td align="right" width="232" style="font-size: 10px; border-right-style: solid; "><b>RECIBO INTERNO N° '.$datos['nro_comprobante'].'</b></td>
        </tr>     
        <tr>
            <td align="left" width="60" style="font-size: 9px; border-left-style: solid; solid; border-top-style: solid;">
                Cliente:
            </td>
            <td align="left" width="210" style="font-size: 9px; solid; border-top-style: solid;">
                <b>'.$datos['nombrePersona'].'</b>
            </td>
            <td align="left" width="60" style="font-size: 9px; border-top-style: solid;">
                Domicilio:
            </td>
            <td align="left" width="210" style="font-size: 9px; border-right-style: solid; border-top-style: solid;">
                <b>'.$datos['domicilio'].'</b>
            </td>
        </tr>        
        <tr>
            <td align="left" width="60" style="font-size: 9px; border-left-style: solid; solid;">
                CUIT:
            </td>
            <td align="left" width="210" style="font-size: 9px; solid; ">
                <b>'.$datos['cuit'].'</b>
            </td>
            <td align="left" width="60" style="font-size: 9px; ">
                Localidad:
            </td>
            <td align="left" width="210" style="font-size: 9px; border-right-style: solid; ">
                <b>'.$datos['localidad'].'</b>
            </td>
        </tr>   
        <tr>
            <td width="540" style="font-size: 10px; border-right-style: solid; border-left-style: solid; solid; ">
                Recibimos la suma de Pesos:
            </td>
        </tr>        
        <tr>
            <td width="540" style="font-size: 12px; border-right-style: solid; border-left-style: solid; solid; ">
            <b>'.\app\controllers\CajaController::numeroToLetra($datos['importe']).'</b>
            </td>
        </tr>        
        <tr>
            <td width="400" style="font-size: 9px; border-left-style: solid; solid;">
                Por los siguientes Conceptos:
            </td>
            <td width="140" align="right" style="font-size: 9px; border-right-style: solid; solid;">
                Importe
            </td>
        </tr>        
        <tr>
            <td width="400" style="font-size: 9px; border-left-style: solid; solid;">
                <b>'.$datos['concepto'].'</b>
            </td>
            <td width="140" align="right" style="font-size: 9px; border-right-style: solid; solid;">
            <b>'.number_format($datos['importe'], 2, ",", ".").'</b>
            </td>
        </tr>   
        <tr>
            <td width="540" height="150" style="font-size: 9px; border-left-style: solid; solid; border-right-style: solid; solid; border-bottom-style: solid; ">
                
            </td>
        </tr>       
        <tr>
            <td width="540" style="font-size: 9px; border-left-style: solid; solid; border-right-style: solid; solid; ">
                Medios de pago
            </td>
        </tr>       
        <tr>
            <td align="left" height="150" style="font-size: 11px; border-right-style: solid; border-left-style: solid; border-bottom-style: solid;">';
            if ($medios != null) {
                $tbl = $tbl.'
                <table border="0" cellpadding="1">
                    <tr style="font-weight: bolder">
                        <td align="left" width="90" style="font-size: 8px; ">Medio</td>
                        <td align="right" width="90" style="font-size: 8px; ">Importe</td>
                        <td align="right" width="20" style="font-size: 8px; "></td>
                        <td align="left" width="300" style="font-size: 8px; ">Detalle</td>
                    </tr>';
                    
                foreach ($medios as $g) {
                    $detalle = '';
                    /*if ($g['id_medio'] == 2 or $g['id_medio'] == 3) {
                        $detalle = 'Tarjeta: '.$g['tarjeta'];
                    }
                    if ($g['id_medio'] == 4) {
                        $detalle =  'Cuenta: '.$g['cuenta'].' Banco: '.$g['banco_cta'];
                    }
                    if ($g['id_medio'] == 5) {
                        $detalle =  '['.$g['tipo'].'] '.' N°: '.$g['nro_cheque'].' Banco: '.$g['banco'];
                    }*/
                $tbl = $tbl .'
                    <tr>
                        <td align="left" width="90" style="font-size: 9px;">'.$g['medio'].'</td>
                        <td align="right" width="90" style="font-size: 9px;">'.number_format(floatval($g['importe']), 2, ",", ".").'</td>
                        <td align="right" width="20" style="font-size: 9px;"></td>
                        <td align="left" width="300" style="font-size: 9px;">'.$detalle.'</td>
                    </tr>';
                }  

                $tbl = $tbl.'</table></td></tr>';                 
            }
        $tbl = $tbl.'        
        <tr>
            <td width="540" style="font-size: 10px; border-left-style: solid; border-right-style: solid;">
                Observaciones: <b>'.$datos['obs'].'</b>
            </td>
        </tr>  
        <tr>
            <td width="400" align="right" style="font-size: 9px; border-left-style: solid; border-top-style: solid;">
                Total: 
            </td>
            <td width="140" style="font-size: 12px; border-right-style: solid; border-top-style: solid;">
                <b>'.number_format($datos['importe'], 2, ",", ".").'</b>
            </td>
        </tr> 
        <tr>
            <td width="180" height="60" align="left" style="font-size: 9px; border-left-style: solid; border-top-style: solid;">
                
            </td>
            <td width="180" height="60" align="left" style="font-size: 9px; border-left-style: solid; border-top-style: solid;">
                
            </td>
            <td width="180" height="60" align="left" style="font-size: 9px; border-right-style: solid; border-left-style: solid; border-top-style: solid;">
                
            </td>
        </tr>          
        <tr>
            <td width="180" align="center" style="font-size: 8px; border-left-style: solid; border-bottom-style: solid;">
                
            </td>
            <td width="180" align="center" style="font-size: 8px; border-left-style: solid; border-bottom-style: solid;">
                ___________________________<br>Firma
            </td>
            <td width="180" align="center" style="font-size: 8px; border-right-style: solid; border-left-style: solid; border-bottom-style: solid;">
                ___________________________<br>N°: Aclaración
            </td>
        </tr>  
        <tr>
            <td width="540" style="font-size: 8px;">
                Usuario: '.$datos['usuario'].'
            </td>
        </tr>           
        </table>';
    echo $tbl; 
}

?>