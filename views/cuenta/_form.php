<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Banco;
use app\models\Sueldosempresas;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\BancoCuenta $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="banco-cuenta-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cuenta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_banco')->dropDownList(
        ArrayHelper::map(Banco::find()->all(),'id','banco'), 
        ['prompt' => 'Seleccionar Banco...'])  ?>

    <?= $form->field($model, 'id_sucursal')->dropDownList(
        ArrayHelper::map(Sueldosempresas::find()->all(),'idEmpresa','Empresa'), 
        ['prompt' => 'Seleccionar Empresa...'])  ?>

    <?= $form->field($model, 'cbu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

    </div>  

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
