<?php

use app\models\Concepto;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ConceptoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Administración de Conceptos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-list-ul"></i> Administración de Conceptos </div>
</div>
<div class="concepto-index" style="padding: 5px">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}{summary}{pager}",
        'options' => ['style' => 'font-size:12px;'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Id',
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:5%'],
            ],
            'concepto',
            [
                'label' => 'Tipo',
                'attribute' => 'tipo',
                'value' => function($model) {
                    return \app\models\ConceptoTipo::findOne(['id' => $model->id_tipo])->tipo;
                },
                'headerOptions' => ['style' => 'width:12%'],
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Concepto $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
                 'header' => '<a class="btn btn-success" href="/index.php?r=concepto/create" style="padding: 5px"><i class="fa fa-plus"></i> Nuevo</a>',
                 'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        if ($model->sistema == 0) {
                            return Html::a('<i style="color: darkblue; font-size: 17px" class="fa fa-edit"></i>', $url, [
                                'title' => Yii::t('app', 'Update')
                            ]);
                        }
                        return '<i style="color: lightgray; font-size: 17px" class="fa fa-edit" title="No editable (concepto de sistema)"></i>';
                    },
                    'view'   => function ($url, $model) {
                        return Html::a('<i style="color: green; font-size: 17px" class="fa fa-eye"></i>', $url, [
                            'title' => Yii::t('app', 'View')
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        if ($model->sistema == 0) {
                            return Html::a('<span style="color: red; font-size: 17px" class="fa fa-trash"></span>', $url, [
                                'title'        => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii',
                                    '¿Eliminar el registro?'),
                                'data-method'  => 'post',
                                'data-pjax'    => '0',
                            ]);
                        }
                        return '<span style="color: lightgray; font-size: 17px" class="fa fa-trash" title="No eliminable (concepto de sistema)"></span>';
                    }
                ],
            ],
        ],
    ]); ?>


</div>

<script>
    function mostrarAyuda() {
        window.open("https://ecopal-cloud.com/ayuda/Parametros.pdf", "_blank");
    }
</script>  
