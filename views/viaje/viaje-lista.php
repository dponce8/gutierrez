<div style="height : <?php if (count($listado) < 12) {echo (60 + (count($listado) * 40));} else {echo '350';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered" id="tabla_movimientos">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">#</th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold">Fecha</th>
            <th style="font-size: 10px; font-weight: bold">Cliente</th>
            <th style="font-size: 10px; font-weight: bold">Origen</th>
            <th style="font-size: 10px; font-weight: bold">Destino</th>
            <th style="font-size: 10px; font-weight: bold">Pasajeros</th>
            <th style="font-size: 10px; font-weight: bold">Salida</th>
            <th style="font-size: 10px; font-weight: bold">Regreso</th>
            <th style="font-size: 10px; font-weight: bold">Empresa</th>
            <th style="font-size: 10px; font-weight: bold">Coche</th>
            <th style="font-size: 10px; font-weight: bold">Total</th>
            <th style="font-size: 10px; font-weight: bold">Anticipo</th>
            <th style="font-size: 10px; font-weight: bold">Chofer 1</th>
            <th style="font-size: 10px; font-weight: bold">Chofer 2</th>
            <th style="font-size: 10px; font-weight: bold">Dirección Origen</th>
            <th style="font-size: 10px; font-weight: bold">Dirección Destino</th>
            <th style="font-size: 10px; font-weight: bold">Usuario</th>
            <th style="font-size: 10px; font-weight: bold">Observaciones</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; foreach ($listado as $m) { ?>
            <tr class="table-light" id="fila_m_<?=$c?>" style="font-size: 12px; cursor: pointer; ">
                <td><?= $c ?></td>
                <td>
                    <span style="color: red; cursor: pointer; font-size: 16px;" title="Elimimnar Viaje" onclick="eliminarViaje(<?= $m['id']?>)"><i class="fa fa-trash"></i></span>  
                </td>
                <td><?= date("d/m/Y",strtotime($m['fecha']))?></td>
                <td><?= $m['cliente']?></td>
                <td><?= $m['local_origen']?></td>
                <td><?= $m['local_destino']?></td>
                <td><?= $m['pasajeros']?></td>
                <td><?= date("d/m/Y",strtotime($m['fecha_salida'])).' '.$m['hora_salida']?></td>
                <td><?= date("d/m/Y",strtotime($m['fecha_regreso'])).' '.$m['hora_regreso']?></td>
                <td><?= $m['empresa']?></td>
                <td><?= $m['coche']?></td>
                <td><?= $m['total']?></td>
                <td><?= $m['anticipo']?></td>
                <td><?= $m['chofer_1']?></td>
                <td><?= $m['chofer_2']?></td>
                <td><?= $m['direccion_origen']?></td>
                <td><?= $m['direccion_destino']?></td>
                <td><?= $m['usuario']?></td>
                <td><?= $m['obs']?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
    </table>
</div>

<input type="hidden" id="h_fila_mov" value="<?=$c?>"/>

<script>
    $(document).ready(function() {
        $('#movModal').modal('hide');
    });

    function eliminarViaje(id) {
        Swal.fire({
            title: 'Viajes',
            text: 'Eliminar Viaje?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("index.php?r=viaje/viaje-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() +
                "&idViaje=" + id + "&eliminar=1"
                    , function (response) {
                        jQuery("#d_viaje_lista").html(response);
                    });
            }
        })
    }

    function imprimirMov(id) {
        window.open("index.php?r=caja/movimiento-imprime&id=" + id);
    }
</script>