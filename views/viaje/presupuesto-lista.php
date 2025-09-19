<div style="height : <?php if (count($listado) < 12) {echo (60 + (count($listado) * 40));} else {echo '350';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered" id="tabla_movimientos">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">N°</th>
                <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold">Fecha</th>
            <th style="font-size: 10px; font-weight: bold">Cliente</th>
            <th style="font-size: 10px; font-weight: bold">Origen</th>
            <th style="font-size: 10px; font-weight: bold">Destino</th>
            <th style="font-size: 10px; font-weight: bold">Pasajeros</th>
            <th style="font-size: 10px; font-weight: bold">Total</th>
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
                    <?php if (Yii::$app->user->identity->id_perfil == 3 and $m['id_estado'] == 0) {?>
                        <span style="color: green; cursor: pointer; font-size: 16px;" title="Aprobar Presupuesto" onclick="aprobarPresupuesto(<?= $m['id']?>)"><i class="fa fa-check"></i></span>  
                    <?php } ?>
                </td>
                <td>
                    <?php if ($m['id_estado'] == 1 or Yii::$app->user->identity->id_perfil == 3) {?>
                        <span style="color: green; cursor: pointer; font-size: 16px;" title="Reimprimir" onclick="imprimirPresupuesto(<?= $m['id']?>)"><i class="fa fa-print"></i></span>  
                    <?php } ?>
                </td>
                <td>
                    <span style="color: red; cursor: pointer; font-size: 16px;" title="Elimimnar Presupuesto" onclick="eliminarPresupuesto(<?= $m['id']?>)"><i class="fa fa-trash"></i></span>  
                </td>
                <td>
                    <span style="color: black; cursor: pointer; font-size: 16px;" title="Editar Presupuesto" onclick="abrirCarga(<?= $m['id']?>)"><i class="fa fa-edit"></i></span>  
                </td>
                <td><?= date("d/m/Y",strtotime($m['fecha']))?></td>
                <td><?= $m['cliente']?></td>
                <td><?= $m['local_origen']?></td>
                <td><?= $m['local_destino']?></td>
                <td><?= $m['pasajeros']?></td>
                <td><?= $m['total']?></td>
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

    function eliminarPresupuesto(id) {
        Swal.fire({
            title: 'Presupuestos',
            text: 'Eliminar Presupuesto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("index.php?r=viaje/presupuesto-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() +
                "&idPresupuesto=" + id + "&eliminar=1"
                    , function (response) {
                        jQuery("#d_presupuesto_lista").html(response);
                    });
            }
        })
    }

    function aprobarPresupuesto(id) {
        Swal.fire({
            title: 'Presupuestos',
            text: 'Aprobar Presupuesto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aprobar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("index.php?r=viaje/presupuesto-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() +
                "&idPresupuesto=" + id + "&aprobar=1"
                    , function (response) {
                        jQuery("#d_presupuesto_lista").html(response);
                    });
            }
        })
    }

    function imprimirPresupuesto(id) {
        window.open("index.php?r=viaje/presupuesto-imprime&id=" + id);
    }
</script>