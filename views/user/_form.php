<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\UserPerfil;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>    

        <div class="col-sm-4">
            <?= $form->field($model, 'apellido')->textInput(['maxlength' => true]) ?>
        </div>    

        <div class="col-sm-4">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>    
    </div>    
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true, 'type' => 'password']) ?>
        </div>    

        <div class="col-sm-4">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>     
    </div>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'id_perfil')->dropDownList(
                ArrayHelper::map(UserPerfil::find()->where(['>','id', 1])->all(),'id','perfil'), ['prompt' => 'Seleccione Perfil' ])  ?>
        </div>    
    </div>    

    <?php /*
    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>    

    <?= $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>    

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'verification_token')->textInput(['maxlength' => true]) ?>

    */ ?>
    

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
