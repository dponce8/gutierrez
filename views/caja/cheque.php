<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-list-alt"></i> Administración de Cheques
    <a style="color: red; font-size: 16px;" title="Ayuda" href="javascript:mostrarAyuda()"> <i class="fa fa-question-circle"></i></a>
    <button type="button" title="Exportar PDF" class="btn btn-danger float-right" style="margin-right: 8px;" onClick="exportarPDF()"><i class="fa fa-file-pdf-o"></i></button>
    <button type="button" title="Exportar Excel" class="btn btn-success float-right" style="margin-right: 8px;" onClick="exportarHoja()"><i class="fa fa-file-excel-o"></i></button>
    <button type="button" title="Cargar Movimiento" class="btn btn-primary float-right" style="width: 38px; margin-right: 8px;" onClick="abrirCargaCheque()"><i class="fa fa-plus"></i></button>
  </div>
</div>

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Filtar por Empresa</label>
        <select class="form-control" id="s_caja_filtro" onChange="filtrarCheques()" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Empresa ...</option>
            <?php foreach ($cajas as $s) {?>
                <option value="<?=$s['idEmpresa']?>"><?=$s['Empresa']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Filtrar por Tipo</label>
        <select class="form-control" id="s_tipo_filtro" onChange="filtrarCheques()" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Tipo ...</option>
            <?php foreach ($tipo as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['tipo']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Filtrar por Estado</label>
        <select class="form-control" id="s_estado_filtro" onChange="filtrarCheques()" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Estado ...</option>
            <?php foreach ($estados as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['estado']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" for="numero">Filtrar por Banco</label>
        <select class="form-control" onChange="filtrarCheques()" id="s_banco_filtro" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Banco ...</option>
            <?php foreach ($bancos as $s) {?>
                <option value="<?=$s['id']?>"><?=$s['banco']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 0px; font-size: 11px;" id="lab_1">Filtrar por Persona</label>
        <select class="form-control" onChange="filtrarCheques()" id="s_persona_filtro" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Persona ...</option>
            <?php foreach ($personas as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['persona']?></option>
            <?php } ?>
        </select>
    </div>
</div>   

<div id="d_mov_lista"></div>

<!-- Modal -->
<div class="modal fade" id="chequeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chequeTitulo">Carga Cheque</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoChequeModal">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        filtrarCheques();
    });

    function filtrarCheques() {
        $.post("index.php?r=caja/cheque-lista&caja="+$('#s_caja_filtro').val()+
        "&banco="+$('#s_banco_filtro').val()+"&tipo=" + $('#s_tipo_filtro').val()+
        "&persona=" + $('#s_persona_filtro').val()+"&estado=" + $('#s_estado_filtro').val()
            , function (response) {
                jQuery("#d_mov_lista").html(response);
            });
    }

    function abrirCargaCheque() {
        $('#chequeTitulo').text('Carga Cheque');
        $.post("index.php?r=caja/cheque-carga" , function (response) {
            jQuery("#contenidoChequeModal").html(response);
        });

        $('#chequeModal').modal('show');
    }  

    function abrirArqueo() {
        $.post("index.php?r=caja/arqueo" , function (response) {
            jQuery("#contenidoArqueoModal").html(response);
        });

        $('#arqueoModal').modal('show');
    }  

    function exportarHoja() {
        // Crear un enlace temporal para la exportación
        var linkTemporal = document.createElement('a');
        linkTemporal.download = 'cheques.xls';
        linkTemporal.href = '#';
        linkTemporal.style.display = 'none';
        
        // Añadir el enlace al DOM
        document.body.appendChild(linkTemporal);
        
        // Ejecutar la exportación
        var resultado = ExcellentExport.excel(linkTemporal, 'tabla_cheques', 'Cheques');
        
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

    function exportarPDF() {
        window.open("index.php?r=caja/cheque-lista-imprime&caja="+$('#s_caja_filtro').val()+
        "&banco="+$('#s_banco_filtro').val()+"&tipo=" + $('#s_tipo_filtro').val()+
        "&persona=" + $('#s_persona_filtro').val()+"&estado=" + $('#s_estado_filtro').val(), "_blank");
    }
</script>