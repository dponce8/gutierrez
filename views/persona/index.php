<?php

use app\models\Persona;
use app\models\PersonaTipo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PersonaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Administración de Personas';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-users"></i> Administración de Personas
    </div>
</div>

<div class="persona-index" style="padding: 5px">
    <?= GridView::widget([
        'pager' => [
            'firstPageLabel' => 'First',
            'lastPageLabel'  => 'Last'
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}{summary}{pager}",
        'options' => ['style' => 'font-size:12px;'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Tipo',
                'attribute' => 'tipoPersona',
                'value' => function($model) {
                    return $model->personaTipo ? $model->personaTipo->tipo : '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'tipoPersona', ArrayHelper::map(PersonaTipo::find()->orderBy('tipo ASC')->all(), 'tipo', 'tipo'), ['class' => 'form-control', 'prompt' => 'Todos...']),
                'headerOptions' => ['style' => 'width:8%'],
            ],

            'apellido',
            'nombre',
            [
                'label' => 'CUIT',
                'attribute' => 'cuit',
                'headerOptions' => ['style' => 'width:12%'],
            ],
            [
                'label' => 'Provincia',
                'attribute' => 'provinciaNombre',
                'value' => function($model) {
                    return $model->provincia ? $model->provincia->provincia : '';
                },
                'headerOptions' => ['style' => 'width:15%'],
            ],
            //'domicilio',
            //'id_localidad',
            //'id_provincia',
            //'fijo',
            //'celular',
            //'email:email',
            //'id_tipo_persona',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Persona $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'header' => '<a class="btn btn-success" href="/index.php?r=persona/create" style="padding: 5px"><i class="fa fa-plus"></i> Nuevo</a>',
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

<script>
</script>    
