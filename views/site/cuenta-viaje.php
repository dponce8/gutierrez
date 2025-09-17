<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Detalles del Viaje #<?= $listado['id'] ?></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Origen - Destino</h6>
                <p class="lead">
                    <?= $listado['local_origen'] ?>, <?= $listado['pcia_origen'] ?> 
                    <i class="fas fa-arrow-right mx-2"></i>
                    <?= $listado['local_destino'] ?>, <?= $listado['pcia_destino'] ?>
                </p>
                
                <h6 class="text-muted mt-4">Fechas</h6>
                <p>
                    <strong>Salida:</strong> <?= date("d/m/Y", strtotime($listado['fecha_salida'])) ?> <?= $listado['hora_salida'] ?><br>
                    <strong>Regreso:</strong> <?= date("d/m/Y", strtotime($listado['fecha_regreso'])) ?> <?= $listado['hora_regreso'] ?>
                </p>
            </div>
            
            <div class="col-md-6">
                <h6 class="text-muted">Vehículo y Personal</h6>
                <p>
                    <strong>Coche:</strong> <?= $listado['coche'] ?><br>
                    <strong>Chofer:</strong> <?= $listado['chofer_1'] ?>
                </p>

                <h6 class="text-muted mt-4">Importes</h6>
                <div class="row">
                    <div class="col-6">
                        <div class="alert alert-info mb-2">
                            <strong>Total:</strong><br>
                            $ <?= number_format(floatval($listado['total']), 2, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="alert alert-success mb-2">
                            <strong>Pagado:</strong><br>
                            $ <?= number_format(floatval($listado['importe_pagado']), 2, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
