<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Banco $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="banco-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'banco')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
