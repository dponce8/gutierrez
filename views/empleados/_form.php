<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Empleadostipo;
use app\models\Sueldoscondiciones;
use app\models\Sueldosempresas;
use app\models\Sueldoscargos;
use app\models\Localidades;

/** @var yii\web\View $this */
/** @var app\models\Empleados $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="empleados-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'Apellido')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'Nombre')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'NroDoc')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'Domicilio')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'IdTipoEmpleado')->dropDownList(ArrayHelper::map(Empleadostipo::find()->all(), 'IdTipoEmpleado', 'TipoEmpleado'), ['prompt' => 'Seleccionar tipo de empleado...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'CUIL')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'Telefono')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'Legajo')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'IdLocalidad')->dropDownList(ArrayHelper::map(Localidades::find()->orderBy('Localidad')->all(), 'IdLocalidad', 'Localidad'), ['prompt' => 'Seleccionar localidad...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'IdCondicion')->dropDownList(ArrayHelper::map(Sueldoscondiciones::find()->all(), 'IdCondicion', 'Condicion'), ['prompt' => 'Seleccionar condición...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'FechaIngreso')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-3">
                         <?= $form->field($model, 'IdCargo')->dropDownList(ArrayHelper::map(Sueldoscargos::find()->all(), 'idCargo', 'Cargo'), ['prompt' => 'Seleccionar cargo...']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'IdEmpresa')->dropDownList(ArrayHelper::map(Sueldosempresas::find()->all(), 'idEmpresa', 'Empresa'), ['prompt' => 'Seleccionar empresa...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'Contribucion')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'Aportes')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
