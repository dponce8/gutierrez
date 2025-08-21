<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Pais;

/** @var yii\web\View $this */
/** @var app\models\Provincia $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="provincia-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'provincia')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'id_pais')->dropDownList(ArrayHelper::map(Pais::find()->orderBy('pais ASC')->all(), 'id', 'pais'), ['prompt' => 'Seleccionar país...']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
