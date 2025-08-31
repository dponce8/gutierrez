<div style="height : <?php if (count($listado) < 12) {echo (60 + (count($listado) * 40));} else {echo '350';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered" id="tabla_movimientos">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">#</th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold">Caja</th>
            <th style="font-size: 10px; font-weight: bold">Fecha</th>
            <th style="font-size: 10px; font-weight: bold">Hora</th>
            <th style="font-size: 10px; font-weight: bold">Concepto</th>
            <th style="font-size: 10px; font-weight: bold">Nro. Recibo</th>
            <th style="font-size: 10px; font-weight: bold">Importe</th>
            <th style="font-size: 10px; font-weight: bold">Persona</th>
            <th style="font-size: 10px; font-weight: bold">Usuario</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; foreach ($listado as $m) { $color='#000000'; if($m['estado'] == 0){$color='red';}?>
            <tr class="table-light" id="fila_m_<?=$c?>" style="font-size: 12px; cursor: pointer; color:<?=$color?>">
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $c ?></td>
                <td>
                    <a style="color: green; font-size: 13px;" title="Reimprimir" href="javascript:imprimirMov(<?= $m['idMov'].','.$c?>)"><i class="fa fa-print"></i></a>
                </td>
                <td>
                    <?php if ($m['estado'] == 1) { ?>
                    <a style="color: red; font-size: 13px;" title="Anular" href="javascript:anularMov(<?= $m['idMov']?>)"><i class="fa fa-times"></i></a>
                    <?php } ?>
                </td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['empresa']?></td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= date("d/m/Y",strtotime($m['fecha']))?></td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['hora']?></td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['concepto']?></td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['nro_comprobante']?></td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)" style="text-align: right;"><?= number_format(floatval($m['importe']), 2, ',', '.')?></td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['persona']?></td>
                <td onClick="verMedios(<?=$m['idMov'].','.$c?>)"><?= $m['usuario']?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
    </table>
</div>

<input type="hidden" id="h_fila_mov" value="<?=$c?>"/>

<script>
    function anularMov(id) {
        Swal.fire({
            title: 'Movimientos',
            text: 'Anular Operación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Anular'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("index.php?r=caja/movimiento-lista&concepto="+$('#s_concepto_filtro').val()+
                    "&persona=" + $('#s_persona_filtro').val()+"&desde=" + $('#desde').val()+
                    "&hasta=" + $('#hasta').val() + "&caja=" + $('#s_caja_filtro').val() +
                    "&idMov=" + id + "&anular=1"
                    , function (response) {
                        jQuery("#d_mov_lista").html(response);
                    });
            }
        })
    }

    function imprimirMov(id) {
        window.open("index.php?r=caja/movimiento-imprime&id=" + id);
    }
</script>