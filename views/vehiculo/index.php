<?php

use app\models\Vehiculo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\VehiculoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Vehiculos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-bus"></i> Vehículos</div>
</div>
<div class="vehiculo-index" style="padding: 5px;">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}{summary}{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'patente',
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
            [
                'attribute' => 'numero_interno',
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
            [
                'attribute' => 'marca',
                'headerOptions' => ['style' => 'width: 20%;'],
            ],
            [
                'attribute' => 'modelo',
                'headerOptions' => ['style' => 'width: 20%;'],
            ],
            [
                'attribute' => 'fabricacion',
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
            [
                'attribute' => 'asientos',
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
            [
                'attribute' => 'fecha_alta',
                'format' => ['date', 'php:d/m/Y'],
                'headerOptions' => ['style' => 'width: 15%;'],
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Vehiculo $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'header' => '<a class="btn btn-success" href="/index.php?r=vehiculo/create" style="padding: 5px"><i class="fa fa-plus"></i> Nuevo</a>',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i style="color: darkblue; font-size: 17px" class="fa fa-edit"></i>', $url, [
                            'title' => Yii::t('app', 'Update')
                        ]);
                    },
                    'view'   => function ($url, $model) {
                        return Html::a('<i style="color: green; font-size: 17px" class="fa fa-eye"></i>', $url, [
                            'title' => Yii::t('app', 'View')
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span style="color: red; font-size: 17px" class="fa fa-trash"></span>', $url, [
                            'title'        => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii',
                                '¿Eliminar el registro?'),
                            'data-method'  => 'post',
                            'data-pjax'    => '0',
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>


</div>
