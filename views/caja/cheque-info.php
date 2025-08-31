<?php if ($infoCheque['id_tipo'] == 2) { // cheque de terceros ?>
<div class="cheque-info-container">
    <div class="cheque-card">
        <div class="cheque-header">
            <h3 class="cheque-number">
                <span>📄</span>
                Cheque Nº <?= $infoCheque['nro_cheque'] ?>
                <span class="cheque-type-badge">Terceros</span>
            </h3>
        </div>
        
        <div class="cheque-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">🏦</span>
                        Banco
                    </div>
                    <p class="info-value"><?= $infoCheque['banco'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">👤</span>
                        Librador
                    </div>
                    <p class="info-value"><?= $infoCheque['librador'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">👥</span>
                        Persona
                    </div>
                    <p class="info-value"><?= $infoCheque['persona'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">🏢</span>
                        Endosado a:
                    </div>
                    <p class="info-value"><?= $infoCheque['proveedor'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">📋</span>
                        Formato
                    </div>
                    <p class="info-value"><?= $infoCheque['formato'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">📊</span>
                        Estado
                    </div>
                    <div class="estado-badge estado-activo">
                        <span>●</span>
                        <?= $infoCheque['estado'] ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">💳</span>
                        Cuenta Acreditación
                    </div>
                    <p class="info-value"><?= $infoCheque['cuenta'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">🏷️</span>
                        Transmisión
                    </div>
                    <p class="info-value"><?= $infoCheque['ordenNombre'] ?></p>
                </div>
                
                <div class="info-item info-item-importe">
                    <div class="info-label">
                        <span class="icon">💰</span>
                        Importe
                    </div>
                    <p class="info-value">$ <?= number_format($infoCheque['importe'], 2, ',', '.') ?></p>
                </div>
            </div>
            
            <div class="dates-container">
                <div class="dates-grid">
                    <div class="date-item">
                        <div class="date-label">
                            <span>💰</span> Fecha de Pago
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_pago'])) ?></div>
                    </div>
                    
                    <div class="date-item">
                        <div class="date-label">
                            <span>⏰</span> Fecha de Vencimiento
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_vto'])) ?></div>
                    </div>
                    
                    <?php if (!empty($infoCheque['fecha_deposito'])): ?>
                    <div class="date-item">
                        <div class="date-label">
                            <span>🏦</span> Fecha de Depósito
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_deposito'])) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($infoCheque['fecha_acredita'])): ?>
                    <div class="date-item">
                        <div class="date-label">
                            <span>✅</span> Fecha de Acreditación
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_acredita'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else {?>
    <div class="cheque-info-container">
    <div class="cheque-card">
        <div class="cheque-header">
            <h3 class="cheque-number">
                <span>📄</span>
                Cheque Nº <?= $infoCheque['nro_cheque'] ?>
                <span class="cheque-type-badge">Propios</span>
            </h3>
        </div>
        
        <div class="cheque-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">🏦</span>
                        Banco
                    </div>
                    <p class="info-value"><?= $infoCheque['banco'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">🏢</span>
                        Pagado a:
                    </div>
                    <p class="info-value"><?= $infoCheque['proveedor'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">📋</span>
                        Formato
                    </div>
                    <p class="info-value"><?= $infoCheque['formato'] ?></p>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">📊</span>
                        Estado
                    </div>
                    <div class="estado-badge estado-activo">
                        <span>●</span>
                        <?= $infoCheque['estado'] ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <span class="icon">🏷️</span>
                        Transmisión
                    </div>
                    <p class="info-value"><?= $infoCheque['ordenNombre'] ?></p>
                </div>
                
                <div class="info-item info-item-importe">
                    <div class="info-label">
                        <span class="icon">💰</span>
                        Importe
                    </div>
                    <p class="info-value">$ <?= number_format($infoCheque['importe'], 2, ',', '.') ?></p>
                </div>
            </div>
            
            <div class="dates-container">
                <div class="dates-grid">
                    <div class="date-item">
                        <div class="date-label">
                            <span>💰</span> Fecha de Pago
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_pago'])) ?></div>
                    </div>
                    
                    <div class="date-item">
                        <div class="date-label">
                            <span>⏰</span> Fecha de Vencimiento
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_vto'])) ?></div>
                    </div>
                    
                    <?php if (!empty($infoCheque['fecha_deposito'])): ?>
                    <div class="date-item">
                        <div class="date-label">
                            <span>🏦</span> Fecha de Depósito
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_deposito'])) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($infoCheque['fecha_acredita'])): ?>
                    <div class="date-item">
                        <div class="date-label">
                            <span>✅</span> Fecha de Acreditación
                        </div>
                        <div class="date-value"><?= date("d/m/Y", strtotime($infoCheque['fecha_acredita'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<style>
    .cheque-info-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 15px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .cheque-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        overflow: hidden;
        margin-bottom: 15px;
        position: relative;
    }
    
    .cheque-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4CAF50, #2196F3, #FF9800);
    }
    
    .cheque-header {
        background: rgba(255,255,255,0.95);
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .cheque-number {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .cheque-type-badge {
        background: #e8f5e8;
        color: #2e7d32;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .cheque-body {
        background: rgba(255,255,255,0.98);
        padding: 20px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    
    .info-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 14px;
        border-left: 4px solid #667eea;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-left-color 0.2s ease;
        position: relative;
        will-change: transform;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        transform: translateZ(0);
    }
    
    .info-item-importe {
        background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
        border: 2px solid #4CAF50;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        transform: scale(1.02);
    }
    
    .info-item-importe:hover {
        transform: scale(1.03) translateY(-1px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
        border-color: #388E3C;
    }
    
    .info-item-importe .info-label {
        color: #2E7D32;
        font-weight: 700;
    }
    
    .info-item-importe .info-value {
        font-size: 18px;
        font-weight: 700;
        color: #1B5E20;
    }
    
    .info-item:hover {
        transform: translateY(-1px) translateZ(0);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left-color: #764ba2;
    }
    
    .info-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    
    .info-value {
        font-size: 15px;
        font-weight: 500;
        color: #2c3e50;
        margin: 0;
        word-break: break-word;
        line-height: 1.3;
    }
    
    .icon {
        font-size: 14px;
        opacity: 0.8;
    }
    
    .estado-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .estado-activo {
        background: #e8f5e8;
        color: #2e7d32;
    }
    
    .estado-pendiente {
        background: #fff3e0;
        color: #f57c00;
    }
    
    .estado-vencido {
        background: #ffebee;
        color: #d32f2f;
    }
    
    .dates-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .dates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }
    
    .date-item {
        text-align: center;
        padding: 12px;
        background: rgba(255,255,255,0.8);
        border-radius: 8px;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .date-label {
        font-size: 10px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    
    .date-value {
        font-size: 14px;
        font-weight: 700;
        color: #2c3e50;
    }
    
    @media (max-width: 992px) {
        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .cheque-info-container {
            padding: 10px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        
        .dates-grid {
            grid-template-columns: 1fr;
        }
        
        .cheque-header {
            padding: 12px 15px;
        }
        
        .cheque-body {
            padding: 15px;
        }
        
        .info-item {
            padding: 12px;
        }
    }
</style>