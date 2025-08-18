<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Localidades $model */

$this->title = $model->IdLocalidad;
$this->params['breadcrumbs'][] = ['label' => 'Localidades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-map"></i> Localidad</div>
</div>
<div class="localidades-view" style="padding: 5px;">

    <p>
        <?= Html::a('Modificar', ['update', 'IdLocalidad' => $model->IdLocalidad], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'IdLocalidad' => $model->IdLocalidad], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Volver', ['index', 'IdLocalidad' => $model->IdLocalidad], ['class' => 'btn btn-warning']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'IdLocalidad',
            'Localidad',
            'id_provincia',
            'codigo_postal',
        ],
    ]) ?>

</div>
