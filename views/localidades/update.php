<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Localidades $model */

$this->title = 'Update Localidades: ' . $model->IdLocalidad;
$this->params['breadcrumbs'][] = ['label' => 'Localidades', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->IdLocalidad, 'url' => ['view', 'IdLocalidad' => $model->IdLocalidad]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-map"></i> Editar Localidad</div>
</div>
<div class="localidades-update" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
