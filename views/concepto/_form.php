<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ConceptoTipo;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Concepto $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="concepto-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'concepto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_tipo')->dropDownList(
                ArrayHelper::map(ConceptoTipo::find()->all(),'id','tipo'))  ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
