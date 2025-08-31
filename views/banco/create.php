<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Banco $model */

$this->title = 'Nuevo Banco';
$this->params['breadcrumbs'][] = ['label' => 'Bancos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-university"></i> Nuevo Banco</div>
</div>
<div class="banco-create" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
