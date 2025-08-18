<?php

use app\models\Localidades;
use app\models\Provincia;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\LocalidadesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Localidades';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-map"></i> Localidades</div>
</div>
<div class="localidades-index" style="padding: 5px;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}{summary}{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'IdLocalidad',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            'Localidad',
            [
                'attribute' => 'id_provincia',
                'value' => function($model) {
                    return Provincia::findOne(['id' => $model->id_provincia])->provincia ?? 'N/A';
                },
                'headerOptions' => ['style' => 'width: 25%;'],
                'filter' => \yii\helpers\ArrayHelper::map(Provincia::find()->orderBy('provincia ASC')->all(), 'id', 'provincia'),
            ],
            'codigo_postal',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Localidades $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'IdLocalidad' => $model->IdLocalidad]);
                },
                'header' => '<a class="btn btn-success" href="/index.php?r=localidades/create" style="padding: 5px"><i class="fa fa-plus"></i> Nuevo</a>',
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
