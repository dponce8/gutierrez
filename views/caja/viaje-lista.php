<label style="margin-bottom: 0px; font-size: 11px;" for="numero">Seleccione Viaje</label>
<select class="form-control" id="s_viaje" style="font-size: 12px; margin-top: 3px; padding-bottom: 0px; height: 26px;" >
    <option value="0">Viajes ...</option>
    <?php foreach ($listado as $t) {?>
        <option value="<?=$t['id']?>"><?='[N° '.$t['id'].'] - '.$t['origen'].' - Total: '.number_format($t['total'], 2, ',', '.').' - Pendiente: '.number_format((floatval($t['total']) - floatval($t['importe_pagado'])), 2, ',', '.')?></option>
    <?php } ?>
</select>