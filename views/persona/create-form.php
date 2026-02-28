<?php
/**
 * Vista solo formulario para cargar en iframe/modal (sin layout del sitio).
 * Incluye Bootstrap y jQuery para que el formulario funcione correctamente.
 * @var app\models\Persona $model
 */
use yii\helpers\Html;
use yii\helpers\Url;

$urlLocalidad = Url::to(['persona/localidad']);
$idProvincia = (int) ($model->id_provincia ?? 0);
$idLocalidad = (int) ($model->id_localidad ?? 0);
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo cliente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <?= Html::csrfMetaTags() ?>
</head>
<body class="p-3">
    <h6 class="mb-3">Nueva persona / cliente</h6>
    <?= $this->render('_form', [
        'model' => $model,
        'popup' => 1,
    ]) ?>
    <script>
        (function() {
            var urlLocalidad = <?= json_encode($urlLocalidad) ?>;
            var idProvincia = <?= $idProvincia ?>;
            var idLocalidad = <?= $idLocalidad ?>;

            function cargarLocalidades(idProv, idLoc) {
                var sep = urlLocalidad.indexOf('?') >= 0 ? '&' : '?';
                var url = urlLocalidad + sep + 'id=' + encodeURIComponent(idProv) + '&idLocalidad=' + encodeURIComponent(idLoc || 0);
                $.get(url, function(data) {
                    $('#persona-id_localidad').html(data);
                });
            }

            $(document).ready(function() {
                if (idProvincia > 0) {
                    cargarLocalidades(idProvincia, idLocalidad);
                }
                $(document).on('change', '#persona-id_provincia', function() {
                    var id = $(this).val() || 0;
                    cargarLocalidades(id, 0);
                });
            });
        })();
    </script>
</body>
</html>
