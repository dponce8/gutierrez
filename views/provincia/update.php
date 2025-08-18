<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Provincia $model */

$this->title = 'Update Provincia: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Provincias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-map-o"></i> Editar Provincia</div>
</div>
<div class="provincia-update" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
