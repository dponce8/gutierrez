<label style="margin-bottom: 0px; font-size: 11px;" id="lab_1">Seleccione Persona</label>
<select class="form-control my-chosen-select" id="s_persona" onChange="mostrarViajes()" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
    <option value="0">Personas ...</option>
    <?php foreach ($personas as $p) {?>
        <option value="<?=$p['id']?>"><?=$p['persona']?></option>
    <?php } ?>
</select>

<script>
    $(document).ready(function() {
        $(".my-chosen-select").chosen();
    });
</script>