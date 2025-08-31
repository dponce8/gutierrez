<div class="row" style="padding-top: 15px; padding-left: 5px; padding-right: 5px;">
    <div class="form-group col-sm-3" style="margin-top: -10px;">
        <label style="margin-bottom: 3px; font-size: 11px;">Filtrar por Persona</label>
        <select class="form-control my-chosen-select" id="s_persona_presu" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
            <option value="0">Cualquier Persona ...</option>
            <?php foreach ($personas as $p) {?>
                <option value="<?=$p['id']?>"><?=$p['persona']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-sm-2" style="margin-top: -10px; ">
        <label style="font-size: 11px; ">Nro Presupuesto</label>
        <input id="nro" type="text" class="form-control">
    </div>
    <div class="form-group col-sm-1" style="margin-top: 8px; "> 
      <button type="button" title="Buscar" class="btn btn-info float-right" style="margin-right: 8px;" onClick="filtrarPresupuestos()"><i class="fa fa-search"></i></button>
    </div>
</div>  

<div id="d_presupuesto_lista"></div>

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
        $("#btnCargarViaje").hide();
    });

    function filtrarPresupuestos() {
        $.post("index.php?r=viaje/viaje-presupuesto-lista&idPersona=" + $("#s_persona_presu").val() + "&nro=" + $("#nro").val() , function (response) {
            jQuery("#d_presupuesto_lista").html(response);
        });
    }
      
</script>