<?php
/**
 * Vista solo formulario para cargar en iframe/modal (sin layout del sitio).
 * @var app\models\Localidades $model
 */
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva localidad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <?= Html::csrfMetaTags() ?>
</head>
<body class="p-3">
    <h6 class="mb-3">Nueva localidad (origen / destino)</h6>
    <?= $this->render('_form', [
        'model' => $model,
        'popup' => 1,
    ]) ?>
</body>
</html>
