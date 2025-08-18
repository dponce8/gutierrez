<?php

use app\models\Empleados;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\Empleadostipo;

/** @var yii\web\View $this */
/** @var app\models\EmpleadosSearc $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Empleados';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-users"></i> Empleados</div>
</div>
<div class="empleados-index" style="padding: 5px;">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,'layout' => "{items}{summary}{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'IdEmpleado',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            'Apellido',
            'Nombre',
            [
                'attribute' => 'NroDoc',
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
            'Domicilio',
            [
                'attribute' => 'IdTipoEmpleado',
                'headerOptions' => ['style' => 'width: 10%;'],
                'value' => function($model) {
                    return Empleadostipo::findOne(['IdTipoEmpleado' => $model->IdTipoEmpleado])->TipoEmpleado ?? 'N/A';
                },
                'filter' => \yii\helpers\ArrayHelper::map(Empleadostipo::find()->all(), 'IdTipoEmpleado', 'TipoEmpleado'),
            ],
            //'CUIL',
            //'Telefono',
            //'Legajo',
            //'IdLocalidad',
            //'IdCondicion',
            //'FechaIngreso',
            //'IdCargo',
            //'IdEmpresa',
            //'IdJornada',
            //'Contribucion',
            //'Aportes',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Empleados $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'IdEmpleado' => $model->IdEmpleado]);
                },
                'header' => '<a class="btn btn-success" href="/index.php?r=empleados/create" style="padding: 5px"><i class="fa fa-plus"></i> Nuevo</a>',
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
