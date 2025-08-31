<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\BancoCuenta $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Banco Cuentas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="banco-cuenta-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cuenta',
            [
                'attribute' => 'id_banco',
                'value' => function($model) {
                    return $model->banco ? $model->banco->banco : '';
                },
                'label' => 'Banco',
            ],
            [
                'attribute' => 'id_sucursal',
                'value' => function($model) {
                    return $model->sucursal ? $model->sucursal->sucursal : '';
                },
                'label' => 'Sucursal',
            ],
            'cbu',
            'alias',
        ],
    ]) ?>

</div>
