    <table class="table table-bordered" >
        <thead class="table-dark">
        <tr style="font-size: 8px;">
            <th colspan="11" align="center" style="font-size: 12px; font-weight: bold">Movimientos <?=date("d/m/Y",strtotime($desde))?> - <?=date("d/m/Y",strtotime($hasta))?></th>
        </tr>
        </thead>
        <thead class="table-dark">
        <tr style="font-size: 8px;">
            <th width="20px" align="center" style="font-size: 8px; font-weight: bold">#</th>
            <th width="100px" style="font-size: 8px; font-weight: bold">Empresa</th>
            <th width="60px" style="font-size: 8px; font-weight: bold">Fecha</th>
            <th width="40px" style="font-size: 8px; font-weight: bold">Hora</th>
            <th width="120px" style="font-size: 8px; font-weight: bold">Concepto</th>
            <th width="50px" style="font-size: 8px; font-weight: bold">N. Comp</th>
            <th width="60px" style="font-size: 8px; font-weight: bold">Tipo Factura</th>
            <th width="60px" style="font-size: 8px; font-weight: bold">Nro. Factura</th>
            <th width="130px" style="font-size: 8px; font-weight: bold">Persona</th>
            <th width="80px" style="font-size: 8px; font-weight: bold">Usuario</th>
            <th width="80px" align="right" style="font-size: 8px; font-weight: bold">Importe</th>
        </tr>
        <tbody>
        <?php $c =1; foreach ($listado as $m) { $color='#000000'; if($m['estado'] == 0){$color='red';}?>
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
    </table>