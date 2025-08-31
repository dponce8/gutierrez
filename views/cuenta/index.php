<?php

use app\models\BancoCuenta;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\BancoCuentaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Cuentas Bancarias';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-indent"></i> Administración de Cuentas Bancarias</div>
</div>
<div class="banco-cuenta-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}{summary}{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Id',
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:4%'],
            ],
            'cuenta',
            [
                'label' => 'Banco',
                'attribute' => 'banco',
                'value' => function($model) {
                    return \app\models\Banco::findOne(['id' => $model->id_banco])->banco;
                },
            ],
            [
                'label' => 'Sucursal',
                'attribute' => 'sucursal',
                'value' => function($model) {
                    return $model->id_sucursal ? \app\models\Sueldosempresas::findOne(['idEmpresa' => $model->id_sucursal])->Empresa : '';
                },
            ],
            'cbu',
            'alias',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, BancoCuenta $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
                 'header' => '<a class="btn btn-success" href="/index.php?r=cuenta/create" style="padding: 5px"><i class="fa fa-plus"></i> Nuevo</a>',
                'template' => '{update} {delete}',
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
                ],
            ],
        ],
    ]); ?>


</div>

<script>
</script>  