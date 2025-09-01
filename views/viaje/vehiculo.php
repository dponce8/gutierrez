<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-bus"></i> Asignación Vehículos</div>
</div>

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-1" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Periodo</label>
        <input type="number" class="form-control" id="periodo" value="<?= date('Y') ?>" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Coche</label>
        <select class="form-control my-chosen-select" id="s_coche" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Todos ...</option>
            <?php foreach ($coches as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['numero_interno'].' ['.$p['asientos'].']'?></option>
            <?php } ?>
        </select>
    </div>
 
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Mes</label>
        <select class="form-control my-chosen-select" id="s_mes" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Mes ...</option>
            <option value="1">Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
        </select>
    </div>
    <div class="form-group col-sm-1" style="margin-top: 8px; "> 
      <button type="button" title="Buscar" class="btn btn-info float-right" style="margin-right: 8px;" onClick="verVehiculo()"><i class="fa fa-bus"></i></button>
    </div>
</div>

<div id="d_vehiculo"></div>


<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
        
        // Eventos para actualizar automáticamente
        $("#s_coche").on('chosen:change', function() {
            verVehiculo();
        });
        
        $("#s_mes").on('chosen:change', function() {
            verVehiculo();
        });
        
        $("#periodo").on('change', function() {
            verVehiculo();
        });
        
        // Mostrar mensaje inicial
        verVehiculo();
    });

    function verVehiculo() {
        var coche = $("#s_coche").val();
        var mes = $("#s_mes").val();
        var periodo = $("#periodo").val();
        
        // Solo hacer la consulta si todos los campos tienen valores válidos
        // Para coches: permitir 0 (todos) o mayor a 0 (específico)
        if ((coche == "0" || coche > 0) && mes > 0 && periodo) {
            $.post("index.php?r=viaje/vehiculo-lista&idCoche=" + coche + "&mes=" + mes + "&periodo=" + periodo , function (response) {
                jQuery("#d_vehiculo").html(response);
            });
        } else {
            jQuery("#d_vehiculo").html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Seleccione un mes y período para ver la disponibilidad.</div>');
        }
    }
      
</script>