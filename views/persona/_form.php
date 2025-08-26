<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Provincia;
use app\models\Localidad;
use app\models\PersonaTipo;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Persona $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="persona-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-4">    
            <?= $form->field($model, 'apellido')->textInput(['maxlength' => true]) ?>
        </div>    

        <div class="col-sm-4">
        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>    

        <div class="col-sm-4">
        <?= $form->field($model, 'cuit')->textInput(['maxlength' => true]) ?>
        </div>    
    </div>    

    <div class="row">
        <div class="col-sm-4">   
        <?= $form->field($model, 'domicilio')->textInput(['maxlength' => true]) ?> 
        </div>    

        <div class="col-sm-4">
            <?= $form->field($model, 'id_provincia')->dropDownList(ArrayHelper::map(Provincia::find()->orderBy('provincia ASC')->all(),'id','provincia'),
            [
                'prompt' => Yii::t('app','Seleccione Provincia'),
                'onchange'=>'
                $.post( "index.php?r=persona/localidad&id='.'"+$(this).val()+"&idLocalidad="+'.(int)$model->id_localidad.', function( data ) {
                $( "select#persona-id_localidad" ).html( data ); })'
            ]
            )->label('Provincia'); ?>
        </div>    

        <div class="col-sm-4">
            <?= $form->field($model, 'id_localidad')->dropDownList([])  ?>
        </div>    
    </div>  

    <div class="row">
        <div class="col-sm-4"> 
            <?= $form->field($model, 'fijo')->textInput(['maxlength' => true]) ?>   
        </div>    

        <div class="col-sm-4">
            <?= $form->field($model, 'celular')->textInput(['maxlength' => true]) ?>
        </div>    

        <div class="col-sm-4">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>    
    </div>  

    <div class="row">
        <div class="col-sm-4"> 
        <?= $form->field($model, 'id_tipo_persona')->dropDownList(
                ArrayHelper::map(PersonaTipo::find()->all(),'id','tipo'))  ?>
        </div>      
    </div>      

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function() {
        $.post( "index.php?r=persona/localidad&id="+<?=$model->id_provincia?>+"&idLocalidad="+<?=$model->id_localidad?>, function( data ) {
                $( "select#persona-id_localidad").html( data ); })
    });
</script>    
