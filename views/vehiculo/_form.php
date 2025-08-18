<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\VehiculoEstado;

/** @var yii\web\View $this */
/** @var app\models\Vehiculo $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="vehiculo-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'patente')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'numero_interno')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'marca')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'modelo')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'fabricacion')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'asientos')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'id_estado')->dropDownList(ArrayHelper::map(VehiculoEstado::find()->all(), 'id', 'estado')) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'fecha_alta')->textInput(['type' => 'date']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'fecha_baja')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-9">
            <?= $form->field($model, 'obs')->textInput() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
