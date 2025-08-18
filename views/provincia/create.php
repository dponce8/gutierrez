<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Provincia $model */

$this->title = 'Create Provincia';
$this->params['breadcrumbs'][] = ['label' => 'Provincias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-map-o"></i> Nueva Provincia</div>
</div>
<div class="provincia-create" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
