<div class="card text-white bg-dark mb-12">
  <div class="card-header">
    <i style="font-size: 17px; padding-right: 10px" class="fa fa-sort-numeric-asc"></i> Saldo de Viajes Especiales
  </div>
</div>

<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
<div class="form-group col-sm-2" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Empresa</label>
        <select class="form-control" id="s_empresa" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Empresa ...</option>
            <?php foreach ($empresas as $p) {?>
                <option value="<?=$p['idEmpresa']?>"><?=$p['Empresa']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-3" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Filtrar por Persona</label>
        <select class="form-control my-chosen-select" id="s_persona" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Persona ...</option>
            <?php foreach ($personas as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['persona']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-1" style="margin-top: 8px; "> 
      <button type="button" title="Buscar" class="btn btn-info float-right" style="margin-right: 8px;" onClick="filtrarViajes()"><i class="fa fa-search"></i></button>
    </div>
</div>  

<div id="d_viaje_lista"></div>

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
        filtrarViajes();
    });

    function filtrarViajes() {
        $.post("index.php?r=viaje/saldo-lista&idPersona=" + $("#s_persona").val() + "&empresa=" + $("#s_empresa").val() , function (response) {
            jQuery("#d_viaje_lista").html(response);
        });
    }      
</script>