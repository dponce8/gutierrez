<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Persona $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Personas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-users"></i> Administración de Personas</div>
</div>
<div class="persona-view" style="padding: 5px;">

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
            'apellido',
            'nombre',
            'cuit',
            'domicilio',
            [
                'attribute' => 'id_localidad',
                'value' => function($model) {
                    return \app\models\Localidades::findOne(['IdLocalidad' => $model->id_localidad])->Localidad;
                },
            ],
            [
                'attribute' => 'id_provincia',
                'value' => function($model) {
                    return \app\models\Provincia::findOne(['id' => $model->id_provincia])->provincia;
                },
            ],
            'fijo',
            'celular',
            'email:email',
            [
                'attribute' => 'id_tipo_persona',
                'value' => function($model) {
                    return \app\models\PersonaTipo::findOne(['id' => $model->id_tipo_persona])->tipo;
                },
            ],
        ],
    ]) ?>

</div>
