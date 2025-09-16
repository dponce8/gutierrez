<div style="height : <?php if (count($listado) < 12) {echo (60 + (count($listado) * 40));} else {echo '350';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered" id="tabla_movimientos">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">N°</th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold">Fecha</th>
            <th style="font-size: 10px; font-weight: bold">Cliente</th>
            <th style="font-size: 10px; font-weight: bold">Origen</th>
            <th style="font-size: 10px; font-weight: bold">Destino</th>
            <th style="font-size: 10px; font-weight: bold">Pasajeros</th>
            <th style="font-size: 10px; font-weight: bold">Total</th>
            <th style="font-size: 10px; font-weight: bold">Fecha Salida</th>
            <th style="font-size: 10px; font-weight: bold">Fecha Regreso</th>
            <th style="font-size: 10px; font-weight: bold">Dirección Origen</th>
            <th style="font-size: 10px; font-weight: bold">Dirección Destino</th>
            <th style="font-size: 10px; font-weight: bold">Usuario</th>
            <th style="font-size: 10px; font-weight: bold">Observaciones</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; foreach ($listado as $m) { ?>
            <tr class="table-light" id="fila_m_<?=$c?>" style="font-size: 12px; cursor: pointer; ">
                <td><?= $m['id'] ?></td>
                <td>
                    <span style="color: green; cursor: pointer; font-size: 16px;" title="Utilizar Presupuesto" onclick="agregarPresupuesto(<?= $m['id']?>)"><i class="fa fa-plus"></i></span>  
                </td>
                <td><?= date("d/m/Y",strtotime($m['fecha']))?></td>
                <td><?= $m['cliente']?></td>
                <td><?= $m['local_origen']?></td>
                <td><?= $m['local_destino']?></td>
                <td><?= $m['pasajeros']?></td>
                <td><?= $m['total']?></td>
                <td><?= date("d/m/Y",strtotime($m['fecha_salida'])).' '.$m['hora_salida']?></td>
                <td><?= date("d/m/Y",strtotime($m['fecha_regreso'])).' '.$m['hora_regreso']?></td>
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
    });

    function agregarPresupuesto(id) {
        Swal.fire({
            title: 'Presupuesto',
            text: 'Agregar Presupuesto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Agregar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("index.php?r=viaje/viaje-carga&id=" + id
                    , function (response) {
                        jQuery("#contenidoMovModal").html(response);
                    });
            }
        })
    }
</script>