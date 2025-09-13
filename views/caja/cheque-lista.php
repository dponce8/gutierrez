<div style="height : <?php if (count($listado) < 8) {echo (60 + (count($listado) * 40));} else {echo '500';}?>px; overflow : auto; white-space: nowrap; overflow-x : auto;">
    <table class="table table-bordered" id="tabla_cheques">
        <thead class="table-dark">
        <tr style="font-size: 10px;">
            <th style="font-size: 10px; font-weight: bold">#</th>
            <th style="font-size: 10px; font-weight: bold">Empresa</th>
            <th style="font-size: 10px; font-weight: bold">Tipo</th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold">Nro. Cheque</th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold"></th>
            <th style="font-size: 10px; font-weight: bold">Banco</th>
            <th style="font-size: 10px; font-weight: bold">Importe</th>
            <th style="font-size: 10px; font-weight: bold">Fecha Pago</th>
            <th style="font-size: 10px; font-weight: bold">Fecha Vto</th>
            <th style="font-size: 10px; font-weight: bold">Persona</th>
            <th style="font-size: 10px; font-weight: bold">Formato</th>
            <th style="font-size: 10px; font-weight: bold">Transmisión</th>
            <th style="font-size: 10px; font-weight: bold">Usuario</th>
            <th style="font-size: 10px; font-weight: bold">Observaciones</th>
            <th style="font-size: 10px; font-weight: bold">F. Depósito</th>
            <th style="font-size: 10px; font-weight: bold">Cuenta</th>
            <th style="font-size: 10px; font-weight: bold">F. Acreditación</th>
            <th style="font-size: 10px; font-weight: bold">Cuenta Débito</th>
            <th style="font-size: 10px; font-weight: bold">F. Débito</th>
        </tr>
        </thead>
        <tbody>
        <?php $c =1; foreach ($listado as $m) { ?>
            <tr class="table-light">
                <td><?= $c ?></td>
                <td><?= $m['Empresa']?></td>
                <td><?= $m['tipo']?></td>
                <td width="30" class="text-center">
                    <a style="color: blue; font-size: 17px;" title="Ver Info" href="javascript:infoCheque(<?= $m['id']?>)"><i class="fa fa-info-circle"></i></a>
                </td>
                <td><?= $m['nro_cheque']?></td>
                <td>
                <?php 
                    $clase = 'success';
                    if ($m['id_estado'] == 3 or $m['id_estado'] == 4 or $m['id_estado'] == 10) {$clase='danger';} 
                    if ($m['id_estado'] == 7 or $m['id_estado'] == 8 or $m['id_estado'] == 9 or $m['id_estado'] == 2) {$clase='warning';} 
                    ?>
                    <span style="border-radius: 1em;" class="label label-pill label-<?=$clase?>"><?=$m['estado']?></span>
                </td>
                <td>
                    <a style="color: green; font-size: 17px;" title="Cambiar Estado" href="javascript:cambiarEstado(<?= $m['id']?>)"><i class="fa fa-arrow-circle-right"></i></a>
                </td>
                <td><?= $m['banco']?></td>
                <td><?= number_format($m['importe'],2, ',', '.')?></td>
                <td><?= date("d/m/Y",strtotime($m['fecha_pago']))?></td>
                <td><?= date("d/m/Y",strtotime($m['fecha_vto']))?></td>
                <td><?= $m['persona']?></td>
                <td><?php if ($m['electronico'] == 1) {echo '<b>F-</b>'.substr('0000'.$m['nro_interno'],-5);} else {echo '<b>E-</b>'.substr('0000'.$m['nro_interno'],-5);}?></td>
                <td><?= $m['ordenNombre']?></td>
                <td><?= $m['usuario']?></td>
                <td><?= $m['obs']?></td>
                <td><?= $m['fecha_deposito'] ? date("d/m/Y",strtotime($m['fecha_deposito'])) : ''?></td>
                <td><?= $m['cuenta']?></td>
                <td><?= $m['fecha_acredita'] ? date("d/m/Y",strtotime($m['fecha_acredita'])) : ''?></td>
                <td><?= $m['cuenta_origen'] ? $m['cuenta_origen'] : ''?></td>
                <td><?= $m['fecha_debito'] ? date("d/m/Y",strtotime($m['fecha_debito'])) : ''?></td>
            </tr>
        <?php $c++; } ?>    
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#chequeModal').modal('hide');
    });
    
    function admCheque(id, estado) {
        var titulo = 'Cancelar Cheque';
        if (estado == 3) {titulo = 'Finalizar Cheque';}
        Swal.fire({
            title: titulo,
            text: '¿Confirma la acción?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("index.php?r=caja/cheque-lista&caja="+$('#s_caja_filtro').val()+
                "&banco="+$('#s_banco_filtro').val()+"&tipo=" + $('#s_tipo_filtro').val()+
                "&persona=" + $('#s_persona_filtro').val()+"&estado=" + $('#s_estado_filtro').val()+"&id="+id+"&newEstado="+estado+"&adm=1"
                , function (response) {
                    jQuery("#d_mov_lista").html(response);
                });
            }
        })    
    }

    
    function cambiarEstado(id) {
        $('#chequeTitulo').text('Cambiar Estado');
        $.post("index.php?r=caja/cheque-cambio&idCheque="+id , function (response) {
            jQuery("#contenidoChequeModal").html(response);
        });

        $('#chequeModal').modal('show');
    }
    
    function infoCheque(id) {
        $('#chequeTitulo').text('Información del Cheque');
        $.post("index.php?r=caja/cheque-info&idCheque="+id , function (response) {
            jQuery("#contenidoChequeModal").html(response);
        });

        $('#chequeModal').modal('show');
    }
</script>