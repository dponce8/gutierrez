<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Vehiculo $model */

$this->title = 'Create Vehiculo';
$this->params['breadcrumbs'][] = ['label' => 'Vehiculos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-bus"></i> Nuevo Vehículo</div>
</div>
<div class="vehiculo-create" style="padding: 5px;">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
