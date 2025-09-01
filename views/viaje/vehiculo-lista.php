<?php
use yii\helpers\Html;

// Obtener parámetros del GET
$mes = Yii::$app->request->get('mes');
$periodo = Yii::$app->request->get('periodo');
$idCoche = Yii::$app->request->get('idCoche');

// Nombres de los meses
$nombresMeses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Colores para diferentes vehículos
$coloresVehiculos = [
    '#dc3545', '#007bff', '#28a745', '#ffc107', '#6f42c1', 
    '#fd7e14', '#20c997', '#e83e8c', '#6c757d', '#17a2b8'
];

// Crear array de días ocupados y mapeo de vehículos
$diasOcupados = [];
$vehiculosEncontrados = [];
$contadorColor = 0;

foreach ($vehiculos as $viaje) {
    $fechaInicio = new DateTime($viaje['fecha_salida']);
    $fechaFin = new DateTime($viaje['fecha_regreso']);
    
    // Asignar color al vehículo si no lo tiene
    if (!isset($vehiculosEncontrados[$viaje['vehiculo_id']])) {
        $vehiculosEncontrados[$viaje['vehiculo_id']] = [
            'numero_interno' => $viaje['numero_interno'],
            'color' => $coloresVehiculos[$contadorColor % count($coloresVehiculos)]
        ];
        $contadorColor++;
    }
    
    // Crear fechas de inicio y fin del mes consultado
    $inicioMes = new DateTime("$periodo-$mes-01");
    $finMes = new DateTime($inicioMes->format('Y-m-t'));
    
    // Ajustar fechas si el viaje se extiende fuera del mes
    $fechaInicioCalculo = max($fechaInicio, $inicioMes);
    $fechaFinCalculo = min($fechaFin, $finMes);
    
    // Marcar todos los días del rango como ocupados
    $current = clone $fechaInicioCalculo;
    while ($current <= $fechaFinCalculo) {
        $dia = (int)$current->format('d');
        if (!isset($diasOcupados[$dia])) {
            $diasOcupados[$dia] = [];
        }
        $diasOcupados[$dia][] = [
            'viaje_id' => $viaje['id'],
            'vehiculo_id' => $viaje['vehiculo_id'],
            'numero_interno' => $viaje['numero_interno'],
            'fecha_salida' => $viaje['fecha_salida_formatted'],
            'fecha_regreso' => $viaje['fecha_regreso_formatted'],
            'color' => $vehiculosEncontrados[$viaje['vehiculo_id']]['color']
        ];
        $current->add(new DateInterval('P1D'));
    }
}

// Calcular información del calendario
$primerDia = new DateTime("$periodo-$mes-01");
$ultimoDia = new DateTime($primerDia->format('Y-m-t'));
$diasEnMes = (int)$ultimoDia->format('d');
$diaSemanaInicio = (int)$primerDia->format('w'); // 0=domingo, 1=lunes, etc.

// Ajustar para que lunes sea 0
$diaSemanaInicio = ($diaSemanaInicio == 0) ? 6 : $diaSemanaInicio - 1;
?>

<div class="calendario-vehiculo">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fa fa-calendar"></i> 
                <?= $idCoche == 0 ? 'Disponibilidad de Todos los Vehículos' : 'Disponibilidad del Vehículo' ?> - <?= $nombresMeses[$mes] ?> <?= $periodo ?>
            </h5>
        </div>
        <div class="card-body p-2">
            
            <!-- Leyenda -->
            <div class="row mb-3">
                <div class="col-12">
                    <?php if ($idCoche == 0 && !empty($vehiculosEncontrados)): ?>
                        <!-- Leyenda para múltiples vehículos -->
                        <h6 class="text-center mb-2">Vehículos:</h6>
                        <div class="d-flex justify-content-center flex-wrap">
                            <?php foreach ($vehiculosEncontrados as $vehiculoInfo): ?>
                                <div class="mr-3 mb-1">
                                    <span style="color: <?= $vehiculoInfo['color'] ?>; font-size: 16px;"><i class="fa fa-square"></i></span> 
                                    <?= $vehiculoInfo['numero_interno'] ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <div class="mr-3">
                                <span class="text-success"><i class="fa fa-check-circle"></i></span> Disponible
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Leyenda para vehículo individual -->
                        <div class="d-flex justify-content-center">
                            <div class="mr-3">
                                <span class="text-success"><i class="fa fa-check-circle"></i></span> Disponible
                            </div>
                            <div class="mr-3">
                                <span class="text-danger"><i class="fa fa-times-circle"></i></span> Ocupado
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Calendario -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm calendario-tabla">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">Lun</th>
                            <th class="text-center">Mar</th>
                            <th class="text-center">Mié</th>
                            <th class="text-center">Jue</th>
                            <th class="text-center">Vie</th>
                            <th class="text-center">Sáb</th>
                            <th class="text-center">Dom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $diaActual = 1;
                        $semanas = ceil(($diasEnMes + $diaSemanaInicio) / 7);
                        
                        for ($semana = 0; $semana < $semanas; $semana++) {
                            echo "<tr>";
                            
                            for ($diaSemana = 0; $diaSemana < 7; $diaSemana++) {
                                if ($semana == 0 && $diaSemana < $diaSemanaInicio) {
                                    // Días vacíos antes del primer día del mes
                                    echo "<td class='text-muted'></td>";
                                } elseif ($diaActual > $diasEnMes) {
                                    // Días vacíos después del último día del mes
                                    echo "<td class='text-muted'></td>";
                                } else {
                                    // Día válido del mes
                                    $esOcupado = isset($diasOcupados[$diaActual]);
                                    $claseEstado = $esOcupado ? 'ocupado' : 'disponible';
                                    
                                    echo "<td class='text-center dia-calendario $claseEstado'>";
                                    echo "<div class='dia-numero'>";
                                    
                                    if ($esOcupado) {
                                        // Si es "todos los vehículos", mostrar badge con buen contraste
                                        if ($idCoche == 0) {
                                            echo "<span class='badge' style='background-color: #6c757d; color: #fff; font-weight: bold;'>" . $diaActual . "</span>";
                                        } else {
                                            // Para vehículo individual, mostrar badge rojo
                                            echo "<span class='badge badge-danger'>" . $diaActual . "</span>";
                                        }
                                    } else {
                                        // Día disponible, siempre verde
                                        echo "<span class='badge badge-success'>" . $diaActual . "</span>";
                                    }
                                    
                                    echo "</div>";
                                    
                                    // Mostrar información de viajes si está ocupado
                                    if ($esOcupado) {
                                        echo "<div class='viajes-info' style='font-size: 10px; margin-top: 2px;'>";
                                        
                                        if ($idCoche == 0) {
                                            // Para todos los vehículos, mostrar con colores
                                            $vehiculosDelDia = [];
                                            foreach ($diasOcupados[$diaActual] as $viaje) {
                                                if (!in_array($viaje['vehiculo_id'], $vehiculosDelDia)) {
                                                    $vehiculosDelDia[] = $viaje['vehiculo_id'];
                                                    echo "<div style='color: {$viaje['color']}; display: inline-block; margin: 1px;' title='Vehículo: {$viaje['numero_interno']} - Viaje ID: {$viaje['viaje_id']} ({$viaje['fecha_salida']} - {$viaje['fecha_regreso']})'>";
                                                    echo "<i class='fa fa-square'></i>";
                                                    echo "</div>";
                                                }
                                            }
                                        } else {
                                            // Para vehículo individual, mostrar icono de bus
                                            foreach ($diasOcupados[$diaActual] as $viaje) {
                                                echo "<div class='text-danger' title='Viaje ID: {$viaje['viaje_id']} ({$viaje['fecha_salida']} - {$viaje['fecha_regreso']})'>";
                                                echo "<i class='fa fa-bus'></i>";
                                                echo "</div>";
                                                break; // Solo mostrar uno para evitar duplicados
                                            }
                                        }
                                        echo "</div>";
                                    }
                                    
                                    echo "</td>";
                                    $diaActual++;
                                }
                            }
                            
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Detalles de viajes -->
            <?php if (!empty($vehiculos)): ?>
            <div class="mt-3">
                <h6>Detalles de Viajes:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>ID Viaje</th>
                                <th>Fecha Salida</th>
                                <th>Fecha Regreso</th>
                                <th>Vehículo</th>
                                <?php if ($idCoche == 0): ?>
                                <th>Color</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehiculos as $viaje): ?>
                            <tr>
                                <td><?= $viaje['id'] ?></td>
                                <td><?= $viaje['fecha_salida_formatted'] ?></td>
                                <td><?= $viaje['fecha_regreso_formatted'] ?></td>
                                <td><?= $viaje['numero_interno'] ?></td>
                                <?php if ($idCoche == 0): ?>
                                <td>
                                    <span style="color: <?= $vehiculosEncontrados[$viaje['vehiculo_id']]['color'] ?>;">
                                        <i class="fa fa-square"></i>
                                    </span>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.calendario-vehiculo .dia-calendario {
    height: 60px;
    vertical-align: top;
    position: relative;
    padding: 5px;
}

.calendario-vehiculo .dia-numero {
    margin-bottom: 2px;
}

.calendario-vehiculo .disponible {
    background-color: #f8f9fa;
}

.calendario-vehiculo .ocupado {
    background-color: #fff5f5;
}

.calendario-vehiculo .viajes-info {
    line-height: 1;
}

.calendario-vehiculo .calendario-tabla td {
    border: 1px solid #dee2e6;
    width: 14.28%;
}

.calendario-vehiculo .calendario-tabla th {
    background-color: #e9ecef;
    font-weight: bold;
    text-align: center;
    padding: 8px 4px;
}

.calendario-vehiculo .badge {
    font-size: 12px;
    min-width: 25px;
}

.calendario-vehiculo .badge-success {
    background-color: #28a745 !important;
    color: white !important;
}

.calendario-vehiculo .badge-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.calendario-vehiculo .table-sm td {
    padding: 0.3rem;
}
</style>
