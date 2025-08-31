<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tarjeta $model */

$this->title = 'Create Tarjeta';
$this->params['breadcrumbs'][] = ['label' => 'Tarjetas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-credit-card"></i> Nueva Tarjeta</div>
</div>
<div class="tarjeta-create" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
