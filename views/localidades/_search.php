<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LocalidadesSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="localidades-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'IdLocalidad') ?>

    <?= $form->field($model, 'Localidad') ?>

    <?= $form->field($model, 'id_provincia') ?>

    <?= $form->field($model, 'codigo_postal') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
