<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Empleados $model */

$this->title = 'Update Empleados: ' . $model->IdEmpleado;
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->IdEmpleado, 'url' => ['view', 'IdEmpleado' => $model->IdEmpleado]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-users"></i> Modificar Empleado</div>
</div>
<div class="empleados-update" style="padding: 5px;">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
