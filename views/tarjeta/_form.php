<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TarjetaTipo;

/** @var yii\web\View $this */
/** @var app\models\Tarjeta $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tarjeta-form" >

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tarjeta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipo')->dropDownList(
                ArrayHelper::map(TarjetaTipo::find()->all(),'id','tipo'))  ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
