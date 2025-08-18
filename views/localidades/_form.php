<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Provincia;

/** @var yii\web\View $this */
/** @var app\models\Localidades $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="localidades-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'Localidad')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'codigo_postal')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'id_provincia')->dropDownList(ArrayHelper::map(Provincia::find()->orderBy('provincia ASC')->all(), 'id', 'provincia'), ['prompt' => 'Seleccionar provincia...']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'IdLocalidad' => $model->IdLocalidad], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
