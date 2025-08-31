<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BancoCuenta $model */

$this->title = 'Nueva Cuenta';
$this->params['breadcrumbs'][] = ['label' => 'Banco Cuentas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-indent"></i> Nueva Cuenta</div>
</div>
<div class="banco-cuenta-create" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
