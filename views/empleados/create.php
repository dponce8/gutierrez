<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Empleados $model */

$this->title = 'Create Empleados';
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-users"></i> Nuevo Empleado</div>
</div>
<div class="empleados-create" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
