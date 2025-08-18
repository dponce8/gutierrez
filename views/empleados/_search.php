<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\EmpleadosSearc $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="empleados-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'IdEmpleado') ?>

    <?= $form->field($model, 'Apellido') ?>

    <?= $form->field($model, 'Nombre') ?>

    <?= $form->field($model, 'NroDoc') ?>

    <?= $form->field($model, 'Domicilio') ?>

    <?php // echo $form->field($model, 'IdTipoEmpleado') ?>

    <?php // echo $form->field($model, 'CUIL') ?>

    <?php // echo $form->field($model, 'Telefono') ?>

    <?php // echo $form->field($model, 'Legajo') ?>

    <?php // echo $form->field($model, 'IdLocalidad') ?>

    <?php // echo $form->field($model, 'IdCondicion') ?>

    <?php // echo $form->field($model, 'FechaIngreso') ?>

    <?php // echo $form->field($model, 'IdCargo') ?>

    <?php // echo $form->field($model, 'IdEmpresa') ?>

    <?php // echo $form->field($model, 'IdJornada') ?>

    <?php // echo $form->field($model, 'Contribucion') ?>

    <?php // echo $form->field($model, 'Aportes') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
