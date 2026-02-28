<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-road"></i> Viajes Especiales
        <button type="button" title="Buscar Presupuesto" class="btn btn-warning float-right" style="margin-right: 8px;" onClick="buscarPresupuesto()"><i class="fa fa-sticky-note-o"></i></button>
        <button type="button" title="Nuevo Viaje" class="btn btn-info float-right" style="margin-right: 8px;" onClick="abrirCarga()"><i class="fa fa-plus"></i></button>
  </div>
</div>

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-3" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Filtrar por Persona</label>
        <select class="form-control my-chosen-select" id="s_persona_filtro" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Persona ...</option>
            <?php foreach ($personas as $p) {?>
                <option value="<?=$p['id']?>" ><?=$p['persona']?></option>
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
      <button type="button" title="Buscar" class="btn btn-info float-right" style="margin-right: 8px;" onClick="filtrarViajes()"><i class="fa fa-search"></i></button>
    </div>
</div>  

<div id="d_viaje_lista"></div>

<!-- Modal -->
<div class="modal fade" id="movModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="movTitulo">Carga de Viaje</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoMovModal">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-primary" id="btnCargarViaje" onClick="cargarViaje()">Cargar Viaje</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modViajeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="movTitulo">Modificar Viaje</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoModViajeModal">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-primary" id="btnModificarViaje" onClick="guardarModViaje()">Modificar Viaje</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" style="min-height: 400px;">
        <iframe id="iframeNuevoCliente" style="width: 100%; height: 500px; border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- Modal Nueva Localidad -->
<div class="modal fade" id="modalNuevoLocalidad" tabindex="-1" aria-labelledby="modalNuevoLocalidadLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nueva localidad</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" style="min-height: 400px;">
        <iframe id="iframeNuevoLocalidad" style="width: 100%; height: 400px; border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
        filtrarViajes();
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
      $("#coord_origen").val('');
      $("#coord_destino").val('');
      $("#s_chofer_1").val(0).trigger('chosen:updated');
      $("#s_chofer_2").val(0).trigger('chosen:updated');
      $("#total").val('');
      $("#anticipo").val('');
      $("#s_medio").val(0);
      $("#obs").val('');
      $("#s_coche").val(0).trigger('chosen:updated');
      $("#pasajeros").val('');
  }


    function abrirCarga() {
        $("#movTitulo").html('Carga de Viaje');
        $.post("index.php?r=viaje/viaje-carga" , function (response) {
            jQuery("#contenidoMovModal").html(response);
        });
        limpiar();

        $('#movModal').modal('show');
    }  

    function filtrarViajes() {
        $.post("index.php?r=viaje/viaje-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() , function (response) {
            jQuery("#d_viaje_lista").html(response);
        });
    }

    function buscarPresupuesto() {
        $("#movTitulo").html('Carga de Presupuesto');
        $.post("index.php?r=viaje/viaje-presupuesto" , function (response) {
            jQuery("#contenidoMovModal").html(response);
        });

        $('#movModal').modal('show');
    } 

    function modificarViaje(id) {
        $("#movTitulo").html('Modificar Viaje');
        $.post("index.php?r=viaje/viaje-modifica&id=" + id , function (response) {
            jQuery("#contenidoModViajeModal").html(response);
        });

        $('#modViajeModal').modal('show');
    }

    window.refreshClientes = function(nuevoId) {
        $.getJSON("index.php?r=viaje/clientes-list", function(data) {
            var $sel = $('#s_cliente');
            if ($sel.length === 0) return;
            var firstOpt = $sel.find('option:first').clone();
            $sel.find('option').remove();
            $sel.append(firstOpt);
            $.each(data, function(i, p) {
                $sel.append($('<option></option>').val(p.id).text((p.apellido || '') + ' ' + (p.nombre || '')));
            });
            $sel.trigger('chosen:updated');
            if (nuevoId) $sel.val(String(nuevoId)).trigger('chosen:updated');
        });
    };

    window.cerrarModalNuevoCliente = function() {
        $('#modalNuevoCliente').modal('hide');
        document.getElementById('iframeNuevoCliente').src = 'about:blank';
    };

    window.tipoLocalidadModal = 'origen';

    window.refreshLocalidades = function(nuevoId) {
        $.getJSON("index.php?r=viaje/localidades-list", function(data) {
            var tipo = window.tipoLocalidadModal || 'origen';
            var $origen = $('#s_origen');
            var $destino = $('#s_destino');
            function llenarSelect($sel) {
                if ($sel.length === 0) return;
                var firstOpt = $sel.find('option:first').clone();
                $sel.find('option').remove();
                $sel.append(firstOpt);
                $.each(data, function(i, row) {
                    var id = row.idlocalidad != null ? row.idlocalidad : row.IdLocalidad;
                    var texto = '[' + (row.pais || '') + '] ' + (row.provincia || '') + ' - ' + (row.localidad || '');
                    $sel.append($('<option></option>').val(id).text(texto));
                });
                $sel.trigger('chosen:updated');
            }
            llenarSelect($origen);
            llenarSelect($destino);
            if (nuevoId && tipo) {
                var $target = (tipo === 'origen') ? $origen : $destino;
                if ($target.length) $target.val(String(nuevoId)).trigger('chosen:updated');
            }
        });
    };

    window.cerrarModalNuevoLocalidad = function() {
        $('#modalNuevoLocalidad').modal('hide');
        document.getElementById('iframeNuevoLocalidad').src = 'about:blank';
    };
      
</script>