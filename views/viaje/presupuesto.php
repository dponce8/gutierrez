<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-sticky-note-o"></i> Presupuestos
        <button type="button" title="Nuevo Presupuesto" class="btn btn-info float-right" style="margin-right: 8px;" onClick="abrirCarga(0)"><i class="fa fa-plus"></i></button>
       
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

<!-- Modal Nuevo Cliente (formulario de persona en iframe) -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoClienteLabel">Nuevo cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 400px;">
        <iframe id="iframeNuevoCliente" style="width: 100%; height: 500px; border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- Modal Nueva Localidad (origen/destino) -->
<div class="modal fade" id="modalNuevoLocalidad" tabindex="-1" aria-labelledby="modalNuevoLocalidadLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoLocalidadLabel">Nueva localidad</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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


    function abrirCarga(id) {
        limpiar();
        $.post("index.php?r=viaje/presupuesto-carga&id=" + id , function (response) {
            jQuery("#contenidoMovModal").html(response);
        });        

        $('#movModal').modal('show');
    }  

    function filtrarPresupuestos() {
        $.post("index.php?r=viaje/presupuesto-lista&idPersona=" + $("#s_persona_filtro").val() + "&desde=" + $("#desde").val() + "&hasta=" + $("#hasta").val() , function (response) {
            jQuery("#d_presupuesto_lista").html(response);
        });
    }

    /**
     * Refresca el select de clientes del modal de presupuesto (llamado desde la ventana de alta de persona en popup).
     * @param {number} [nuevoId] Si se indica, selecciona este cliente después de refrescar.
     */
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
            if (nuevoId) {
                $sel.val(String(nuevoId)).trigger('chosen:updated');
            }
        });
    };

    /**
     * Cierra el modal de nuevo cliente (llamado desde el iframe tras guardar).
     */
    window.cerrarModalNuevoCliente = function() {
        $('#modalNuevoCliente').modal('hide');
        document.getElementById('iframeNuevoCliente').src = 'about:blank';
    };

    /** 'origen' o 'destino': en qué select seleccionar la nueva localidad tras refrescar */
    window.tipoLocalidadModal = 'origen';

    /**
     * Refresca los selects de origen y destino; opcionalmente selecciona la localidad recién creada.
     * @param {number} [nuevoId] Id de la localidad a seleccionar.
     */
    window.refreshLocalidades = function(nuevoId) {
        $.getJSON("index.php?r=viaje/localidades-list", function(data) {
            var tipo = window.tipoLocalidadModal || 'origen';
            var $origen = $('#s_origen');
            var $destino = $('#s_destino');
            function llenarSelect($sel, firstLabel) {
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
            llenarSelect($origen, 'Origen ...');
            llenarSelect($destino, 'Destino ...');
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