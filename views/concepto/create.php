<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Concepto $model */

$this->title = 'Nuevo Concepto';
$this->params['breadcrumbs'][] = ['label' => 'Conceptos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
  <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-list-ul"></i> Nuevo Concepto</div>
</div>
<div class="concepto-create" style="padding: 5px">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
