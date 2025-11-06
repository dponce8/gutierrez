    <table class="table table-bordered" >
        <thead class="table-dark">
        <tr style="font-size: 8px; height: 50px;">
            <th colspan="11" align="center" style="font-size: 12px; font-weight: bold; padding: 15px;">Movimientos <?=date("d/m/Y",strtotime($desde))?> - <?=date("d/m/Y",strtotime($hasta))?></th>
        </tr>
        </thead>
        <thead class="table-dark">
        <tr style="font-size: 8px; background-color: #495057; height: 35px;">
            <th width="20px" align="center" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">#</th>
            <th width="100px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Empresa</th>
            <th width="60px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Fecha</th>
            <th width="40px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Hora</th>
            <th width="120px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Concepto</th>
            <th width="50px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">N. Comp</th>
            <th width="60px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Tipo Factura</th>
            <th width="60px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Nro. Factura</th>
            <th width="130px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Persona</th>
            <th width="80px" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Usuario</th>
            <th width="80px" align="right" style="font-size: 8px; font-weight: bold; padding: 8px; color: white;">Importe</th>
        </tr>
        <tbody>
        <?php $c =1; $total = 0; foreach ($listado as $m) { 
            
            $total = $total + $m['importe'];
            $color='#000000'; if($m['estado'] == 0){$color='red';}?>
            <tr class="table-light" id="fila_m_<?=$c?>" style="font-size: 8px; cursor: pointer; color:<?=$color?>">
                <td width="20px" align="center" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $c ?></td>
                <td width="100px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['empresa']?></td>
                <td width="60px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= date("d/m/Y",strtotime($m['fecha']))?></td>
                <td width="40px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['hora']?></td>
                <td width="120px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['concepto']?></td>
                <td width="50px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['nro_comprobante']?></td>
                <td width="60px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['tipo_factura']?></td>
                <td width="60px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['nro_factura']?></td>
                <td width="130px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['persona']?></td>
                <td width="80px" onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['usuario']?></td>
                <td width="80px" onClick="verMedios(<?=$m['idMov'].','.$c?>)" style="text-align: right;"><?= number_format(floatval($m['importe']), 2, ',', '.')?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
        <tfoot>
            <tr style="background-color: #6c757d; height: 35px;">
                <td colspan="11" align="right" style="font-size: 10px; font-weight: bold; padding: 10px; color: white;">Total: <?= number_format(floatval($total), 2, ',', '.')?></td>
            </tr>
        </tfoot>
    </table>