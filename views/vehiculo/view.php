<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Vehiculo $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Vehiculos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
    <div class="card text-white bg-dark mb-12" >
        <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-bus"></i> Vehículo</div>
    </div>
<div class="vehiculo-view" style="padding: 5px;">

    <p>
        <?= Html::a('Modificar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'patente',
            'numero_interno',
            'marca',
            'modelo',
            'fabricacion',
            'asientos',
            [
                'attribute' => 'id_estado',
                'value' => function($model) {
                    return \app\models\VehiculoEstado::findOne(['id' => $model->id_estado])->estado;
                },
            ],
            'fecha_alta',
            'fecha_baja',
            'obs:ntext',
        ],
    ]) ?>

</div>
