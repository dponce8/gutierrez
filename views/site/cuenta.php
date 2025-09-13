
<?php 
use app\models\PersonaTipo;
?>
<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-book"></i> Cuenta Corriente <?=PersonaTipo::findOne(['id' => $tipo])->tipo?>

    <button type="button" title="Excel Cuenta Gral" class="btn btn-danger float-right" style="margin-right: 8px;" onClick="exportarExcelGral(<?=$tipo?>)"><i class="fa fa-file-excel-o"></i></button>
    <button type="button" title="Reporte General" class="btn btn-danger float-right" style="margin-right: 8px;" onClick="reporteGeneral(<?=$tipo?>)"><i class="fa fa-bar-chart"></i></button>
    <button type="button" title="Exportar PDF" class="btn btn-danger float-right" style="margin-right: 28px;" onClick="exportarPDF()"><i class="fa fa-file-pdf-o"></i></button>
    <button type="button" title="Exportar Excel" class="btn btn-success float-right" style="margin-right: 8px;" onClick="exportarHoja()"><i class="fa fa-file-excel-o"></i></button>
  </div>
</div>

<div class="row" style="padding-top: 10px; padding-left: 5px; padding-right: 5px;">
      <div class="form-group col-sm-2" style="margin-top: -10px;">
          <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Empresa</label>
          <select class="form-control" id="s_sucursal" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
              <option value="0">Empresas ...</option>
              <?php foreach ($sucursales as $s) {?>
                  <option value="<?=$s['idEmpresa']?>"><?=$s['Empresa']?></option>
              <?php } ?>
          </select>
      </div>
      <div class="form-group col-sm-3" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Seleccione Persona</label>
        <select class="form-control my-chosen-select" id="s_persona" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Todas ...</option>
            <?php foreach ($personas as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['apellido'].' '.$p['nombre']?></option>
            <?php } ?>
        </select>
    </div>
      <div class="form-group col-sm-1" style="margin-top: 8px;">        
          <button type="button" title="Actualizar" class="btn btn-primary float-right" style="width: 38px; margin-right: 8px;" onClick="verPersonas(<?=$tipo?>, 0)"><i class="fa fa-refresh"></i></button>
      </div>
</div>

<div id="d_cuenta_persona"> </div>

<!-- Modal -->
<div class="modal fade" id="ajusteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ajusteTitulo">Cargar  Ajuste</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoModal">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-primary" onClick="cargarAjuste()">Cargar Ajuste</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="arqueoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="arqueoTitulo">Arqueo Caja</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoArqueoModal">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
      </div>
    </div>
  </div>
</div>

<div id="d_detalle" style="padding-top: 10px;"></div>
<div id="d_detalle_gral" style="padding-top: 10px;"></div>

<input type="hidden" id="h_id_persona"/>
<input type="hidden" id="h_fila_persona"/>
<input type="hidden" id="h_tipo_persona" value="<?=$tipo?>"/>

<script>
    $(document).ready(function() {
        verPersonas($('#h_tipo_persona').val(), 0);
    }); 

    function mostrarDetalle(id, fila) {
        $.post("index.php?r=site/cuenta-lista&id=" + id + "&sucursal=" + $('#s_sucursal').val(), function (response) {
            jQuery("#d_detalle").html(response);
        });

        marcarFilaPersona(fila);
    }

    function verPersonas(tipo, fila) {
      document.getElementById("d_cuenta_persona").innerHTML="";
        $.post("index.php?r=site/cuenta-persona&tipo="+tipo+"&fila="+fila+"&sucursal="+$('#s_sucursal').val()+"&idPersona="+$('#s_persona').val(), function (response) {
            jQuery("#d_cuenta_persona").html(response);
        });
        $('#d_detalle').html('');
    }

    function exportarExcelGral(tipo) {
        $.post("index.php?r=site/cuenta-gral-excel&tipo="+tipo+"&sucursal="+$('#s_sucursal').val(), function (response) {
            jQuery("#d_detalle_gral").html(response);
        });
    }

    function abrirAjuste(id, fila) {
        mostrarDetalle(id, fila);

        $.post("index.php?r=site/cuenta-ajuste&id=" + id + "&fila=" + fila, function (response) {
            jQuery("#contenidoModal").html(response);
        });

        $('#h_id_persona').val(id);
        $('#h_fila_persona').val(fila);

        $('#ajusteModal').modal('show');
    }    

    function marcarFilaPersona(fila) {
        var clases=$('#h_fila_per').val();
        for (var i=1;i< clases;i++ ){
            $("#fila_p_"+i).attr("class","table-light");
        }
        $('#fila_p_'+fila).attr("class", "table-success");
    }

    function exportarHoja() {
        // Crear un enlace temporal para la exportación
        var linkTemporal = document.createElement('a');
        linkTemporal.download = 'cuenta_corriente.xls';
        linkTemporal.href = '#';
        linkTemporal.style.display = 'none';
        
        // Añadir el enlace al DOM
        document.body.appendChild(linkTemporal);
        
        // Ejecutar la exportación
        var resultado = ExcellentExport.excel(linkTemporal, 'tabla_cta_cte', 'Cuenta Corriente');
        
        // Si la exportación fue exitosa, activar la descarga
        if (resultado) {
            linkTemporal.click();
        }
        
        // Limpiar el enlace temporal
        setTimeout(function() {
            if (linkTemporal.parentNode) {
                document.body.removeChild(linkTemporal);
            }
        }, 100);
    }

    function reporteGeneral(tipo) {
        window.open("index.php?r=site/cuenta-gral-imprime&tipo=" + tipo + "&sucursal=" + $('#s_sucursal').val());
    }

    function exportarPDF() {
        window.open("index.php?r=site/cuenta-persona-imprime&tipo=" + $('#h_tipo_persona').val() + "&sucursal=" + $('#s_sucursal').val() + "&idPersona=" + $('#s_persona').val(), "_blank");
    }
</script>