<?php
use yii\helpers\Html;

$tbl =
'<table border="0" cellpadding="1">
    <tr>
        <td width="530" style="font-size: 9px;" align="left">
            ' . Html::img('@web/images/logo.png', ['width' => '220px']) . '
        </td>
    </tr>
    <tr>
        <td align="center" width="530" style="font-size: 14px; font-weight: bold;">PRESUPUESTO</td>
    </tr>
    <tr>
        <td align="right" width="530" style="font-size: 12px; font-weight: bold;">N° 000-'.(substr('0000'.$datos['id'], -5)).' </td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">Fecha: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . date("d/m/Y") . '</td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">Razón Social: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . $datos['cliente'] . '</td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">DNI / CUIT: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . $datos['cuit'] . '</td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">Fecha Vto: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . date("d/m/Y", strtotime($datos['fecha_vto'])) . '</td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">Domicilio: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . $datos['domicilio'] . '</td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">Mail: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . $datos['email'] . '</td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">Teléfono Celular: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . $datos['celular'] . '</td>
    </tr>
    <tr>
        <td align="left" width="100" style="font-size: 10px;">Teléfono Fijo: </td>
        <td align="left" width="430" style="font-size: 10px; font-weight: bold;">' . $datos['fijo'] . '</td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="center" width="530" style="font-size: 12px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">DATOS DEL VIAJE </td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="center" width="133" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">FECHA DE INICIO </td>
        <td align="center" width="132" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">HORA DE INICIO </td>
        <td align="center" width="132" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">FECHA DE FIN </td>
        <td align="center" width="133" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">HORA DE FIN </td>
    </tr>
    <tr>
        <td align="center" width="133" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . date("d/m/Y",  strtotime($datos['fecha_salida'])) . '</td>
        <td align="center" width="132" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['hora_salida'] . '</td>
        <td align="center" width="132" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . date("d/m/Y", strtotime($datos['fecha_regreso'])) . '</td>
        <td align="center" width="133" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['hora_regreso'] . '</td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="center" width="265" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">PROVINCIA ORIGEN </td>
        <td align="center" width="265" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">PROVINCIA DESTINO </td>
    </tr>
    <tr>
        <td align="center" width="265" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['pais_origen']. ' - ' . $datos['pcia_origen'] . '</td>
        <td align="center" width="265" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['pais_destino']. ' - ' . $datos['pcia_destino'] . '</td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="center" width="265" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">DIRECCION ORIGEN </td>
        <td align="center" width="265" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">DIRECCION DESTINO </td>
    </tr>
    <tr>
        <td align="center" width="265" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['direccion_origen'] . '</td>
        <td align="center" width="265" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['direccion_destino'] . '</td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="center" width="265" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">CANTIDAD DE PASAJEROS </td>
        <td align="center" width="265" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['pasajeros'] . '</td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="center" width="530" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">PROGRAMACIÓN TURÍSTICA</td>
    </tr>
    <tr>
        <td align="left" width="530" height="100" style="font-size: 10px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . $datos['obs'] . '</td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="left" width="530" style="font-size: 9px;">En caso de suspensión del viaje "NO" se reintegra el dinero entregado como anticipo, quedando el mismo como seña para realizar otro viaje dentro de los treinta (30) días a partir de la fecha.</td>
    </tr>
    <tr>
        <td align="left" width="150" style="font-size: 10px;;"> </td>
    </tr>
    <tr>
        <td align="center" width="530" style="font-size: 10px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">PRECIO FINAL</td>
    </tr>
    <tr>
        <td align="center" width="530" style="font-size: 12px; font-weight: bold; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid;">' . number_format($datos['total'], 2, ',', '.')  . '</td>
    </tr>
    ';
$tbl = $tbl.'</table>';
echo $tbl; 
?>