<label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Cheque</label>
<select class="form-control" id="s_cheque" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
    <option value="0">Cheques ...</option>
    <?php foreach ($cheques as $t) {?>
        <option importe="<?=$t['importe']?>" value="<?=$t['id']?>"><?='['.$t['tipo'].'] '.$t['banco'].' '.' N°: '.$t['nro_cheque'].' | $'.$t['importe']?></option>
    <?php } ?>
</select>

<script>
    $('#s_cheque').change(function() {
        var opt = $(this.options[this.selectedIndex]);
        var code = opt.attr('importe');
        $('#importe').val(code);
    });
</script>