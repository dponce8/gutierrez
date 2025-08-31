<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-sticky-note-o"></i> Presupuestos
        <button type="button" title="Nuevo Presupuesto" class="btn btn-info float-right" style="margin-right: 8px;" onClick="abrirCarga()"><i class="fa fa-plus"></i></button>
       
  </div>
</div>

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-3" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Filtrar por Persona</label>
        <select class="form-control my-chosen-select" id="s_persona_filtro" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Persona ...</option>
            <?php foreach ($personas as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['persona']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Desde</label>
        <input id="desde" type="date" class="form-control" value="<?=date("Y-m-d")?>">
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Hasta</label>
        <input id="hasta" type="date" class="form-control" value="<?=date("Y-m-d")?>">
    </div>
    <div class="form-group col-sm-1" style="margin-top: 8px; "> 
      <button type="button" title="Buscar" class="btn btn-info float-right" style="margin-right: 8px;" onClick="filtrarPresupuestos()"><i class="fa fa-search"></i></button>
    </div>
</div>  

<div id="d_presupuesto_lista"></div>

<!-- Modal -->
<div class="modal fade" id="movModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="movTitulo">Carga de Presupuesto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoMovModal">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-primary" onClick="cargarPresupuesto()">Cargar Presupuesto</button>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
        filtrarPresupuestos();
    });

  function limpiar() {
      $("#s_cliente").val(0).trigger('chosen:updated');
      var today = new Date().toISOString().split('T')[0];
      var now = new Date().toTimeString().slice(0,5);
      $("#fecha_salida").val(today);
      $("#hora_salida").val(now);
      $("#fecha_regreso").val(today);
      $("#hora_regreso").val(now);
      $("#s_origen").val(0).trigger('chosen:updated');
      $("#s_destino").val(0).trigger('chosen:updated');
      $("#direccion_origen").val('');
      $("#direccion_destino").val('');
      $("#total").val('');
      $("#obs").val('');
  }


    function abrirCarga() {
        $.post("index.php?r=viaje/presupuesto-carga" , function (response) {
            jQuery("#contenidoMovModal").html(response);
        });
        limpiar();

        $('#movModal').modal('show');
    }  

    function filtrarPresupuestos() {
        $.post("index.php?r=viaje/presupuesto-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() , function (response) {
            jQuery("#d_presupuesto_lista").html(response);
        });
    }
      
</script>