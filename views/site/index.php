<?php
use app\controllers\SiteController;

/** @var yii\web\View $this */
/** @var array $viajesProximos */

?>
<div class="row" style="padding: 10px">
    <div class="col-sm-12">  
        
    </div>
</div>

<!-- Modal de Alertas de Viajes Próximos -->
<?php if (!empty($viajesProximos)): ?>
<div class="modal fade" id="viajesProximosModal" tabindex="-1" role="dialog" aria-labelledby="viajesProximosModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <!-- Header con color sólido -->
            <div class="modal-header" style="background: #6c5ce7; color: white; border-radius: 15px 15px 0 0; border: none;">
                <div style="display: flex; align-items: center; width: 100%;">
                    <i class="fa fa-bell-o" style="font-size: 24px; margin-right: 15px; animation: pulse 2s infinite;"></i>
                    <div>
                        <h4 class="modal-title" style="margin: 0; font-weight: 600;">🚗 Viajes Próximos</h4>
                        <small style="opacity: 0.9;">Recordatorio de viajes programados</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8; font-size: 28px; font-weight: 300;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Body del modal -->
            <div class="modal-body" style="padding: 0;">
                <div style="padding: 25px 30px;">
                    <div style="background: #ffeaa7; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                        <i class="fa fa-info-circle" style="color: #e17055; font-size: 20px; margin-right: 8px;"></i>
                        <strong style="color: #e17055;">Tienes <?= count($viajesProximos) ?> viaje<?= count($viajesProximos) > 1 ? 's' : '' ?> programado<?= count($viajesProximos) > 1 ? 's' : '' ?> en los próximos 2 días</strong>
                    </div>
                    
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($viajesProximos as $index => $viaje): ?>
                            <div class="viaje-card" style="
                                background: white; 
                                border: 1px solid #e9ecef; 
                                border-radius: 12px; 
                                padding: 20px; 
                                margin-bottom: 15px; 
                                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            ">
                                <!-- Indicador de urgencia -->
                                <div style="
                                    position: absolute; 
                                    top: 0; 
                                    left: 0; 
                                    width: 5px; 
                                    height: 100%; 
                                    background: <?= $viaje['dias_restantes'] == 0 ? '#e74c3c' : ($viaje['dias_restantes'] == 1 ? '#f39c12' : '#27ae60') ?>;
                                "></div>
                                
                                <div style="margin-left: 15px;">
                                    <!-- Header del viaje -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                        <div>
                                            <h5 style="margin: 0; color: #2d3436; font-weight: 600;">
                                                <i class="fa fa-map-marker" style="color: #6c5ce7; margin-right: 8px;"></i>
                                                Viaje #<?= $viaje['id'] ?>
                                            </h5>
                                            <small style="color: #636e72; font-weight: 500;">
                                                <?php 
                                                if ($viaje['dias_restantes'] == 0) {
                                                    echo '🔴 HOY';
                                                } elseif ($viaje['dias_restantes'] == 1) {
                                                    echo '🟡 MAÑANA';
                                                } else {
                                                    echo '🟢 En ' . $viaje['dias_restantes'] . ' días';
                                                }
                                                ?>
                                            </small>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 18px; font-weight: 600; color: #2d3436;">
                                                <?= date('d/m/Y', strtotime($viaje['fecha_salida'])) ?>
                                            </div>
                                            <div style="color: #6c5ce7; font-weight: 500;">
                                                <?= $viaje['hora_salida'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información del viaje -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div style="margin-bottom: 10px;">
                                                <i class="fa fa-user" style="color: #0984e3; width: 20px;"></i>
                                                <strong>Cliente:</strong> <?= $viaje['cliente'] ?>
                                            </div>
                                            <div style="margin-bottom: 10px;">
                                                <i class="fa fa-users" style="color: #00b894; width: 20px;"></i>
                                                <strong>Pasajeros:</strong> <?= $viaje['pasajeros'] ?>
                                            </div>
                                            <?php if ($viaje['vehiculo']): ?>
                                            <div style="margin-bottom: 10px;">
                                                <i class="fa fa-car" style="color: #e17055; width: 20px;"></i>
                                                <strong>Vehículo:</strong> <?= $viaje['vehiculo'] ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <div style="margin-bottom: 10px;">
                                                <i class="fa fa-map-marker" style="color: #00b894; width: 20px;"></i>
                                                <strong>Origen:</strong> <?= $viaje['origen'] ?>
                                            </div>
                                            <div style="margin-bottom: 10px;">
                                                <i class="fa fa-flag-checkered" style="color: #e74c3c; width: 20px;"></i>
                                                <strong>Destino:</strong> <?= $viaje['destino'] ?>
                                            </div>
                                            <?php if ($viaje['chofer_1']): ?>
                                            <div style="margin-bottom: 10px;">
                                                <i class="fa fa-user-circle" style="color: #6c5ce7; width: 20px;"></i>
                                                <strong>Chofer:</strong> <?= $viaje['chofer_1'] ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer" style="background: #f8f9fa; border-radius: 0 0 15px 15px; border: none; padding: 20px 30px;">
                <button type="button" class="btn btn-primary" data-dismiss="modal" style="
                    background: #6c5ce7; 
                    border: none; 
                    border-radius: 25px; 
                    padding: 10px 25px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                ">
                    <i class="fa fa-check-circle" style="margin-right: 8px;"></i>
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.viaje-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12) !important;
}

.modal-footer .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.4);
    background: #5a4fcf !important;
}
</style>

<script>
    $(document).ready(function() {
        // Mostrar el modal automáticamente si hay viajes próximos
        <?php if (!empty($viajesProximos)): ?>
        $('#viajesProximosModal').modal('show');
        <?php endif; ?>
    });
</script>

