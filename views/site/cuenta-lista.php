<div style="height : <?php if (count($listado) < 12) {echo (50 + (count($listado) * 30));} else {echo '350';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">#</th>
            <th style="font-size: 10px; font-weight: bold">Fecha</th>
            <th style="font-size: 10px; font-weight: bold">Hora</th>
            <th style="font-size: 10px; font-weight: bold">Operación</th>
            <th style="font-size: 10px; font-weight: bold">Debe</th>
            <th style="font-size: 10px; font-weight: bold">Haber</th>
            <th style="font-size: 10px; font-weight: bold">Saldo</th>
            <th style="font-size: 10px; font-weight: bold">Usuario</th>
            <th style="font-size: 10px; font-weight: bold">Observaciones</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; foreach ($listado as $m) { ?>
            <tr class="table-light" id="fila_m_<?=$c?>" style="cursor: pointer;">
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)"><?= $c ?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)"><?= date("d/m/Y",strtotime($m['fecha']))?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)"><?= $m['hora']?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)"><?= $m['tipoMovimiento']?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)" align="right"><?= number_format(floatval($m['debe']),2, ',', '.')?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)" align="right"><?= number_format(floatval($m['haber']),2, ',', '.')?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)" align="right"><?= number_format(floatval($m['saldo']),2, ',', '.')?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)"><?= $m['usuario']?></td>
                <td onClick="verMedios(<?=(int)$m['idMov'].','.(int)$m['id_movimiento_tipo'].','.(int)$m['id_viaje'].','.$c?>)"><?= $m['obs']?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
    </table>
</div>

<input type="hidden" id="h_fila_mov" value="<?=$c?>"/>

<script>
    function verMedios(id,idTipo, idHoja, fila) {
        if (idTipo == 2) {
            if (id > 0) {
                document.getElementById("arqueoTitulo").innerHTML="Medios de Pago"; 
                $.post("index.php?r=caja/movimiento-medio-lista&idMov="+id , function (response) {
                    jQuery("#contenidoArqueoModal").html(response);
                });
            }
        } else {
            if (idHoja > 0) {
                document.getElementById("arqueoTitulo").innerHTML="Hoja de Ruta"; 
                $.post("index.php?r=site/cuenta-hoja&id="+idHoja+"&tipoPersona="+$('#h_tipo_persona').val() , function (response) {
                    jQuery("#contenidoArqueoModal").html(response);
                });
            }
        }

        marcarFilaMov(fila);

        $('#arqueoModal').modal('show');
    } 

    function marcarFilaMov(fila) {
        var clases=$('#h_fila_mov').val();
        for (var i=1;i< clases;i++ ){
            $("#fila_m_"+i).attr("class","table-light");
        }
        $('#fila_m_'+fila).attr("class", "table-warning");
    }
</script>    