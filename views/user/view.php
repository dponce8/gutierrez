<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = $model->id.' - '.$model->apellido.' '.$model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-users"></i> Administración de Usuarios</div>
</div>
<div class="row">
    <div class="col-md-4 text-center">
        <img width="150" src="<?= \Yii::getAlias('@web/images/usr_img/'.\app\models\User::findOne(['id' => $model->id])->img); ?>" class="img-circle" alt="User Image"/>

        <form class="form-horizontal"name="enviar_archivo_administrador" id="enviar_archivo_administrador" method="post" enctype="multipart/form-data">
            <fieldset>
                <input type="hidden" name="idUsuario" id="idUsuario" value="<?=$model->id?>"/>
                <!-- images -->
                <p>
                <div class="form-group">
                    <div class="col-md-12">
                        <label for="file" class="btn btn-primary"  >Selecciona Archivo</label>
                        <input id="file" name="file" style="visibility:hidden"  type="file" size="1">
                    </div>
                </div>

                <!-- Button (Double) -->
                <div class="form-group">
                    <div class="col-md-12">
                        <button type="submit" id="btnGuardar" name="button2id" class="btn btn-primary">Subir Imagen Perfil</button>
                    </div>
                </div>

            </fieldset>
        </form>

        <div id="resultado"></div>
    </div>

    <div class="col-md-8">        
        <div class="user-view" style="padding: 5px">
        <p>
            <?= Html::a('Modificar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Volver', ['index', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </p>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'username',
                'apellido',
                'nombre',
                [
                    'attribute' => 'password_hash',
                    'value' => '**************************',
                ],
                'email:email',
                [
                    'attribute' => 'id_perfil',
                    'value' => function($model) {
                        return \app\models\UserPerfil::findOne(['id' => $model->id_perfil])->perfil;
                    },
                ],
            ],
        ]) ?>
        </div>
    </div>
</div>

<script>
    $("form#enviar_archivo_administrador").submit(function () {
        var formData = new FormData(this);
        $.ajax({
            //url: "index.php?r=user/view&id=1",
            url: "index.php?r=user/img-perfil",
            type: "POST",
            async: false,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function () {
                $("#resultado").html("Procesando, espere por favor");
            },
            success: function (response) {
                $("#resultado").html(response);
            }
        });
        event.preventDefault();
        return false;
    });
</script>    
