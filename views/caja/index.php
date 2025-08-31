<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-usd"></i> Movimientos Caja  
    <a style="color: red; font-size: 16px;" title="Ayuda" href="javascript:mostrarAyuda()"> <i class="fa fa-question-circle"></i></a>
        <button type="button" title="Exportar PDF" class="btn btn-danger float-right" style="margin-right: 8px;" onClick="exportarPDF()"><i class="fa fa-file-pdf-o"></i></button>
        <button type="button" title="Exportar Excel" class="btn btn-success float-right" style="margin-right: 8px;" onClick="exportarHoja()"><i class="fa fa-file-excel-o"></i></button>
        <button type="button" title="Cargar Movimiento" class="btn btn-primary float-right" style="width: 38px; margin-right: 8px;" onClick="abrirCarga()"><i class="fa fa-plus"></i></button>
  </div>
</div>

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Caja</label>
        <select class="form-control" id="s_caja_filtro" onChange="filtrarMovimientos()" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Empresas ...</option>
            <?php foreach ($cajas as $s) {?>
                <option value="<?=$s['idEmpresa']?>"><?=$s['Empresa']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Filtrar por Concepto</label>
        <select class="form-control" onChange="filtrarMovimientos()" id="s_concepto_filtro" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Concepto ...</option>
            <?php foreach ($conceptos as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['concepto']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;" id="lab_1">Filtrar por Persona</label>
        <select class="form-control my-chosen-select" onChange="filtrarMovimientos()" id="s_persona_filtro" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
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
</div>   

<div id="d_mov_lista"></div>

<!-- Modal -->
<div class="modal fade" id="movModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="movTitulo">Carga Ingreso / Egreso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoMovModal">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-primary" onClick="cargarMovimiento()">Cargar Movimiento</button>
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

<script>
    $(document).ready(function() {
        filtrarMovimientos();
        $(".my-chosen-select").chosen();
    });

    function mostrarAyuda() {
        window.open("https://ecopal-cloud.com/ayuda/Movimientos.pdf", "_blank");
    }

    function filtrarMovimientos() {
        $.post("index.php?r=caja/movimiento-lista&concepto="+$('#s_concepto_filtro').val()+
        "&persona=" + $('#s_persona_filtro').val()+"&desde=" + $('#desde').val()+
        "&hasta=" + $('#hasta').val() + "&caja=" + $('#s_caja_filtro').val()
            , function (response) {
                jQuery("#d_mov_lista").html(response);
            });
    }

    function abrirCarga() {
        $.post("index.php?r=caja/movimiento-carga" , function (response) {
            jQuery("#contenidoMovModal").html(response);
        });

        $('#movModal').modal('show');
    }  

    function verMedios(id, fila) {
        document.getElementById("arqueoTitulo").innerHTML="Medios de Pago"; 

        $.post("index.php?r=caja/movimiento-medio-lista&idMov="+id , function (response) {
            jQuery("#contenidoArqueoModal").html(response);
        });

        marcarFilaMov(fila);

        $('#arqueoModal').modal('show');
    }  

    function marcarFilaMov(fila) {
        var clases=$('#h_fila_mov').val();
        for (var i=1;i< clases;i++ ){
            $("#fila_m_"+i).attr("class","table-light");
        }
        $('#fila_m_'+fila).attr("class", "table-success");
    }

    function abrirArqueo() {
        $.post("index.php?r=caja/arqueo" , function (response) {
            jQuery("#contenidoArqueoModal").html(response);
        });

        $('#arqueoModal').modal('show');
    }  

    function exportarPDF() {
        window.open("index.php?r=caja/movimiento-lista-imprime&concepto="+$('#s_concepto_filtro').val()+
        "&persona=" + $('#s_persona_filtro').val()+"&desde=" + $('#desde').val()+
        "&hasta=" + $('#hasta').val() + "&caja=" + $('#s_caja_filtro').val(), "_blank");
    }

    function exportarHoja() {
        // Crear un enlace temporal para la exportación
        var linkTemporal = document.createElement('a');
        linkTemporal.download = 'movimientos_caja.xls';
        linkTemporal.href = '#';
        linkTemporal.style.display = 'none';
        
        // Añadir el enlace al DOM
        document.body.appendChild(linkTemporal);
        
        // Ejecutar la exportación
        var resultado = ExcellentExport.excel(linkTemporal, 'tabla_movimientos', 'Movimientos Caja');
        
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
</script>