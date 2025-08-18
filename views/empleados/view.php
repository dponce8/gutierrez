<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Empleadostipo;
use app\models\Localidades;
use app\models\Sueldoscondiciones;
use app\models\Sueldoscargos;
use app\models\Sueldosempresas;

/** @var yii\web\View $this */
/** @var app\models\Empleados $model */

$this->title = $model->IdEmpleado;
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card text-white bg-dark mb-12" >
    <div class="card-header"><i style="font-size: 17px; padding-right: 10px" class="fa fa-users"></i> Empleado</div>
</div>
<div class="empleados-view" style="padding: 5px;">

<p>
        <?= Html::a('Modificar', ['update', 'IdEmpleado' => $model->IdEmpleado], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'IdEmpleado' => $model->IdEmpleado], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Volver', ['index', 'IdEmpleado' => $model->IdEmpleado], ['class' => 'btn btn-warning']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'IdEmpleado',
            'Apellido',
            'Nombre',
            'NroDoc',
            'Domicilio',
            [
                'attribute' => 'IdTipoEmpleado',
                'value' => function($model) {
                    return Empleadostipo::findOne(['IdTipoEmpleado' => $model->IdTipoEmpleado])->TipoEmpleado ?? 'N/A';
                },
            ],
            'CUIL',
            'Telefono',
            'Legajo',
            [
                'attribute' => 'IdLocalidad',
                'value' => function($model) {
                    return Localidades::findOne(['IdLocalidad' => $model->IdLocalidad])->Localidad ?? 'N/A';
                },
            ],
            [
                'attribute' => 'IdCondicion',
                'value' => function($model) {
                    return Sueldoscondiciones::findOne(['IdCondicion' => $model->IdCondicion])->Condicion ?? 'N/A';
                },
            ],
            'FechaIngreso',
            [
                'attribute' => 'IdCargo',
                'value' => function($model) {
                    return Sueldoscargos::findOne(['idCargo' => $model->IdCargo])->Cargo ?? 'N/A';
                },
            ],
            [
                'attribute' => 'IdEmpresa',
                'value' => function($model) {
                    return Sueldosempresas::findOne(['idEmpresa' => $model->IdEmpresa])->Empresa ?? 'N/A';
                },
            ],
            'Contribucion',
            'Aportes',
        ],
    ]) ?>

</div>
