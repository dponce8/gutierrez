<?php

namespace app\controllers;
use app\models\Caja;
use app\models\Sucursal;
use app\models\Concepto;
use Yii;
use TCPDF;
use DateTime;
use DateTimeZone;
use app\commands\AfipWsaaService;

class CajaController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            // Definir acciones que NO requieren autenticación
            $publicActions = ['login', 'error', 'captcha', 'logout'];
            
            // Si la acción actual está en la lista de acciones públicas, permitir acceso
            if (in_array($action->id, $publicActions)) {
                return true;
            }
            
            // Para el resto de acciones, verificar autenticación
            if (\Yii::$app->getUser()->isGuest) {
                $this->redirect(Yii::$app->urlManager->createAbsoluteUrl('site/login'));
                return false;
            }
            return true;
        }
        return false;
    }
    public function actionIndex()
    {        
        $db = Yii::$app->db;
        $personas = $db->createCommand("select id,concat(apellido,' ',nombre) persona from persona order by apellido, nombre")->queryAll();
        $conceptos = $db->createCommand("select * from concepto order by concepto")->queryAll();
        $cajas = $db->createCommand("select * from sueldosempresas")->queryAll();

        return $this->render('index',['personas' =>$personas, 'conceptos' => $conceptos, 'cajas' => $cajas]);
    }

    public function actionMovimientoCarga()
    {
        $db = Yii::$app->db;

        $conceptos = $db->createCommand("select * from concepto order by concepto")->queryAll();
        $medios = $db->createCommand("select * from medio_pago order by orden")->queryAll();
        $cajas = $db->createCommand("select * from sueldosempresas")->queryAll();

        $cuentas = $db->createCommand("select * from banco_cuenta")->queryAll();
        $creditos = $db->createCommand("select * from tarjeta where tipo = 1")->queryAll();
        $debitos = $db->createCommand("select * from tarjeta where tipo = 2")->queryAll();
        $facturas = $db->createCommand("select * from factura_tipo")->queryAll();

        return $this->renderPartial('movimiento-carga', ['conceptos' => $conceptos, 
        'medios' => $medios, 'cajas' => $cajas, 
        'cuentas' => $cuentas, 'creditos' => $creditos, 'debitos' => $debitos, 'facturas' => $facturas]);
    }

    public function actionMovimientoLista($concepto, $persona, $desde, $hasta, $caja)
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $idMov = $request->get('idMov');

        $caja1 = $caja; $caja2 = $caja;
        if ($caja == 0) {$caja1 = 1; $caja2 = 10;}
        $con1 = $concepto; $con2 = $concepto;
        if ($concepto == 0) {$con1 = 1; $con2 = 10000;}
        $per1 = $persona; $per2 = $persona;
        if ($persona == 0) {$per1 = 0; $per2 = 10000;}

        if ($request->get('anular') == 1) {
            $transaction = \Yii::$app->db->beginTransaction();
            $datosAnulacion = $db->createCommand("select m.*,c.concepto, concat(u.apellido,' ',u.nombre) usuario,
            c.id_tipo tipoConcepto, concat(p.apellido,' ',p.nombre) nombrePersona, p.domicilio,
            l.localidad, p.cuit, ft.tipo_afip, ft.nota_afip
            from movimiento m join persona p on p.id = m.id_persona
            join concepto c on c.id = m.id_concepto
            join user u on u.id = m.id_usuario
            join factura_tipo ft on ft.id = m.id_factura
            left join localidades l on l.idlocalidad = p.id_localidad
            where m.id = :id;")
            ->bindValue(':id', $idMov)
            ->queryOne();

            if (($datosAnulacion['cae'] != null or $datosAnulacion['cae'] != '') and $datosAnulacion['nota_afip'] > 0) {
                $resultadoCae = self::getCaeDirecto($idMov, $datosAnulacion['nota_afip']);
            }

            $db->createCommand("update movimiento set estado = 0 where id = :idMov")
            ->bindValue(':idMov', $idMov)
            ->execute();

            $db->createCommand("delete from persona_movimiento where id_movimiento_caja = :idMov")
            ->bindValue(':idMov', $idMov)
            ->execute();

            $transaction->commit();
        }

        $listado = $db->createCommand("select m.*,p.id,m.id idMov,concat(p.apellido,' ', p.nombre) persona, c.concepto, ca.empresa,
        concat(u.apellido,' ',u.nombre) usuario, ft.tipo tipo_factura
        from movimiento m         
        join concepto c on c.id = m.id_concepto
        join sueldosempresas ca on ca.idEmpresa = m.id_empresa
        join user u on u.id = m.id_usuario
        left join persona p on p.id = m.id_persona
        left join factura_tipo ft on ft.id = m.id_factura
        where m.id_empresa between :caj1 and :caj2 and m.id_concepto between :con1 and :con2 
        and m.id_persona between :per1 and :per2 and m.fecha between :desde and :hasta 
        order by m.fecha desc, m.hora desc")
        ->bindValue(':caj1', $caja1)
        ->bindValue(':caj2', $caja2)
        ->bindValue(':con1', $con1)
        ->bindValue(':con2', $con2)
        ->bindValue(':per1', $per1)
        ->bindValue(':per2', $per2)
        ->bindValue(':desde', $desde)
        ->bindValue(':hasta', $hasta)
        ->queryAll();

        return $this->renderPartial('movimiento-lista',['listado' =>$listado]);
    }

    public static function getNroComprobante($tipoConcepto) {
        $db = Yii::$app->db;
        $db->createCommand("update comprobante set numero = numero + 1 where id = :id")
            ->bindValue(':id', (int)$tipoConcepto)            
            ->execute();

        $recibos = $db->createCommand("select numero from comprobante where id = :id")
            ->bindValue(':id', (int)$tipoConcepto)
            ->queryOne();

        $nroRecibo = $recibos['numero'];   
        return $nroRecibo;
    }

    public function actionMovimientoGuarda($caja, $concepto, $persona, $importe, $obs, $factura, $nro_factura, $id_viaje = 0)
    {
        $db = Yii::$app->db;
        $salida = 0;

        $transaction = \Yii::$app->db->beginTransaction();

        $tipoConcepto = Concepto::findOne(['id' => $concepto])->id_tipo;

        $nroRecibo = self::getNroComprobante($tipoConcepto);    

        $db->createCommand("insert into movimiento (id_empresa,fecha, hora, id_concepto, id_persona, id_usuario, importe, obs, nro_comprobante, id_viaje, id_factura,nro_factura) 
        values(:caja,curdate(), curtime(), :id_concepto, :id_persona, :id_usuario, :importe,:obs, :nroRecibo, :id_viaje, :id_factura, :nro_factura)")
        ->bindValue(':id_concepto', $concepto)
        ->bindValue(':id_persona', $persona)
        ->bindValue(':id_usuario', Yii::$app->user->identity->id)
        ->bindValue(':importe', floatval($importe))
        ->bindValue(':obs', $obs)
        ->bindValue(':caja', $caja)
        ->bindValue(':nroRecibo', $nroRecibo)
        ->bindValue(':id_viaje', $id_viaje)
        ->bindValue(':id_factura', $factura)
        ->bindValue(':nro_factura', $nro_factura)
        ->execute();

        $ultMov = $db->createCommand("select max(id) ultId from movimiento;")->queryOne();
        $idMov = $ultMov["ultId"];

        $db->createCommand("insert into movimiento_medio (id_movimiento, id_medio, importe,id_cheque, id_cuenta, id_tarjeta)
            select :idMov, id_medio, importe,id_cheque, id_cuenta, id_tarjeta
            from movimiento_carga where id_usuario = :id")
            ->bindValue(':idMov', (int)$idMov)
            ->bindValue(':id', Yii::$app->user->identity->id)
            ->execute();

        $db->createCommand("update cheque c 
            join movimiento_carga m on m.id_medio = 5 and m.id_cheque = c.id
            set id_estado = 
            case when c.id_tipo = 1 then 
                2
            else 
                case when :tipoConcepto = 1 then
                    6
                else
                    9
                end
            end
            where m.id_usuario = :id")
            ->bindValue(':id', Yii::$app->user->identity->id)
            ->bindValue(':tipoConcepto', $tipoConcepto)
            ->execute();      

        $db->createCommand("delete from movimiento_carga where id_usuario = :id")
            ->bindValue(':id', Yii::$app->user->identity->id)
            ->execute();    

        if ($concepto == 3) { // Pago Proveedor
            $db->createCommand("insert into persona_movimiento (id_persona, id_movimiento_tipo, importe, fecha, hora, id_usuario, id_empresa, obs, id_movimiento_caja) 
            values(:id_persona, 2, :importe, curdate(), curtime(), :id_usuario, :id_sucursal, :obs, :idMov)")
            ->bindValue(':id_persona', (int)$persona)
            ->bindValue(':importe', floatval($importe))
            ->bindValue(':id_sucursal', Caja::findOne(['id' => $caja])->id_sucursal)
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->bindValue(':obs', $obs)
            ->bindValue(':idMov', $idMov)
            ->execute();
        }

        if ($concepto == 2) { // Cobro Cliente
            $db->createCommand("insert into persona_movimiento (id_persona, id_movimiento_tipo, importe, fecha, hora, id_usuario, id_empresa, obs, id_movimiento_caja, id_viaje) 
            values(:id_persona, 2, :importe, curdate(), curtime(), :id_usuario, :id_sucursal, :obs, :idMov, :id_viaje)")
            ->bindValue(':id_persona', (int)$persona)
            ->bindValue(':importe', floatval($importe))
            ->bindValue(':id_sucursal', $caja)
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->bindValue(':obs', $obs)
            ->bindValue(':idMov', $idMov)
            ->bindValue(':id_viaje', $id_viaje)
            ->execute();
        }

        $transaction->commit();
        $salida = 1;
        return $this->renderPartial('movimiento-guarda',['salida' =>$salida, 'idMov' => $idMov, 'concepto' => $concepto]);
    }    

    public function actionArqueo()
    {
        $db = Yii::$app->db;
        $cajas = $db->createCommand("select * from caja")->queryAll();
        $usuarios = $db->createCommand("select * from user")->queryAll();

        return $this->renderPartial('arqueo',['cajas' => $cajas, 'usuarios' => $usuarios]);
    }

    public function actionArqueoLista($caja, $desde, $hasta, $usuario)
    {
        $db = Yii::$app->db;
        $caj1 = $caja; $caj2 = $caja;
        if ($caja == 0) {$caj1 = 1; $caj2 = 1000;}
        $user1 = $usuario; $user2 = $usuario;
        if ($usuario == 0) {$user1 = 1; $user2 = 1000;}

        $listado = $db->createCommand("select me.id, me.medio, u.id,concat(u.apellido,' ',u.nombre) usuario,
        sum(case when c.id_tipo = 1 then mm.importe else mm.importe*-1 end) importe
        from medio_pago me
        join movimiento_medio mm on mm.id_medio = me.id
        join movimiento m on m.id = mm.id_movimiento
        join concepto c on c.id = m.id_concepto
        join user u on u.id = m.id_usuario
        where m.id_caja between :caj1 and :caj2 and m.id_usuario between :user1 and :user2
        and fecha between :desde and :hasta and m.estado = 1
        group by me.id, me.medio,u.id,u.apellido, u.nombre
        order by u.id, me.id;")
            ->bindValue(':caj1', (int)$caj1)
            ->bindValue(':caj2', (int)$caj2)
            ->bindValue(':user1', (int)$user1)
            ->bindValue(':user2', (int)$user2)
            ->bindValue(':desde', $desde)
            ->bindValue(':hasta', $hasta)
            ->queryAll();

        return $this->renderPartial('arqueo-lista',['listado' => $listado]);
    }

    public function actionCheque()
    {
        $db = Yii::$app->db;
        $bancos = $db->createCommand("select * from banco")->queryAll();
        $cajas = $db->createCommand("select * from sueldosempresas")->queryAll();
        $personas = $db->createCommand("select id,concat(apellido,' ',nombre) persona from persona order by apellido, nombre")->queryAll();
        $tipos = $db->createCommand("select * from cheque_tipo")->queryAll();
        $estados = $db->createCommand("select * from cheque_estado")->queryAll();

        return $this->render('cheque',['bancos' => $bancos, 'cajas' => $cajas, 'personas' => $personas,
        'tipo' => $tipos, 'estados' => $estados]);
    }

    public function actionChequeCarga($fromMov = 0)
    {
        $db = Yii::$app->db;
        $bancos = $db->createCommand("select * from banco")->queryAll();
        $cajas = $db->createCommand("select * from sueldosempresas")->queryAll();
        $personas = $db->createCommand("select id,concat(apellido,' ',nombre) persona from persona where id > 0 order by apellido, nombre")->queryAll();
        $tipos = $db->createCommand("select * from cheque_tipo")->queryAll();
        $formatos = $db->createCommand("select * from cheque_electronico")->queryAll();
        $tiposOrden = $db->createCommand("select * from cheque_orden")->queryAll();
        $cuentas = $db->createCommand( "select * from banco_cuenta")->queryAll();

        return $this->renderPartial('cheque-carga',['bancos' => $bancos, 'cajas' => $cajas, 'personas' => $personas,
        'tipo' => $tipos, 'tiposOrden' => $tiposOrden, 'formatos' => $formatos, 
        'fromMov' => $fromMov, 'cuentas' => $cuentas]);
    } 

    public function actionChequeLista($caja, $banco, $tipo, $persona, $estado,
    $id=0, $newEstado=0, $adm=0, $cuenta=0)
    {
        $db = Yii::$app->db;

        $caja1 = $caja; $caja2 = $caja;
        if ($caja == 0) {$caja1 = 1; $caja2 = 10;}
        $bco1 = $banco; $bco2 = $banco;
        if ($banco == 0) {$bco1 = 1; $bco2 = 10000;}
        $per1 = $persona; $per2 = $persona;
        if ($persona == 0) {$per1 = 0; $per2 = 10000;}
        $tip1 = $tipo; $tip2 = $tipo;
        if ($tipo == 0) {$tip1 = 0; $tip2 = 10000;}
        $est1 = $estado; $est2 = $estado;
        if ($estado == 0) {$est1 = 0; $est2 = 100;}

        if ($adm == 1) {
            $transaction = \Yii::$app->db->beginTransaction();
            $db->createCommand("update cheque set id_estado = :estado where id = :id")
            ->bindValue(':id', $id)
            ->bindValue(':estado', $newEstado)            
            ->execute();

            if ($newEstado == 7) {
                $db->createCommand("update cheque set fecha_deposito = curdate(), id_cuenta = :cuenta  where id = :id")
                ->bindValue(':id', $id)
                ->bindValue(':cuenta', (int)$cuenta)
                ->execute();
            }

            if ($newEstado == 8) {
                $db->createCommand("update cheque set fecha_acredita = curdate() where id = :id")
                ->bindValue(':id', $id)
                ->execute();

                $infoCheque = $db->createCommand("select * from cheque where id = :id")->bindValue(':id', $id)->queryOne();

                // Cargar movimiento par aumentar el saldo de la cuenta                

                $tipoConcepto = Concepto::findOne(['id' => 16])->id_tipo;
                $nroRecibo = self::getNroComprobante($tipoConcepto);    

                $db->createCommand("insert into movimiento (id_caja,fecha, hora, id_concepto, id_persona, id_usuario, importe, obs, nro_comprobante, cotizacion_dolar, importe_dolar, numero_recibo) 
                values(:caja,curdate(), curtime(), :id_concepto, :id_persona, :id_usuario, :importe,:obs, :nroRecibo, :cotizacion, :importeDolar, :numero_recibo_manual)")
                ->bindValue(':id_concepto', 16)
                ->bindValue(':id_persona', 0)
                ->bindValue(':id_usuario', Yii::$app->user->identity->id)
                ->bindValue(':importe', floatval($infoCheque['importe']))
                ->bindValue(':obs', 'Cheque acreditado')
                ->bindValue(':caja', $infoCheque['id_caja'])
                ->bindValue(':nroRecibo', $nroRecibo)
                ->bindValue(':cotizacion', floatval(self::getCotizacion()))
                ->bindValue(':importeDolar', floatval($infoCheque['importe']) / floatval(self::getCotizacion()))
                ->bindValue(':numero_recibo_manual', 0)
                ->execute();

                $ultMov = $db->createCommand("select max(id) ultId from movimiento;")->queryOne();
                $idMov = $ultMov["ultId"];

                $db->createCommand("insert into movimiento_medio (id_movimiento, id_medio, importe,id_cheque, id_cuenta, id_tarjeta)
                    values(:idMov, 4, :importe,0, :id_cuenta, 0)")
                    ->bindValue(':idMov', (int)$idMov)
                    ->bindValue(':importe', floatval($infoCheque['importe']))
                    ->bindValue(':id_cuenta', $infoCheque['id_cuenta'])
                    ->execute();                
            }

            $transaction->commit();
        }

        $listado = $db->createCommand("
        select c.*, t.tipo, ca.Empresa, concat(p.apellido,' ',p.nombre) persona, b.banco,
        concat(u.apellido,' ',u.nombre) usuario, e.estado, DATE_ADD(c.fecha_pago, INTERVAL 1 month) fecha_vto,
        ce.formato, co.tipo ordenNombre, bc.cuenta, bc1.cuenta cuenta_origen
        from cheque c
        join cheque_tipo t on t.id = c.id_tipo and c.id_tipo between :tip1 and :tip2 
        join sueldosempresas ca on ca.idEmpresa = c.id_caja and c.id_caja between :caj1 and :caj2
        join cheque_estado e on e.id = c.id_estado
        join user u on u.id = c.id_usuario
        left join persona p on p.id = c.id_persona and c.id_persona between :per1 and :per2
        left join banco b on b.id = c.id_banco and c.id_banco between :bco1 and :bco2               
        left join cheque_electronico ce on ce.id = c.electronico
        left join cheque_orden co on co.id = c.orden
        left join banco_cuenta bc on bc.id = c.id_cuenta
        left join banco_cuenta bc1 on bc1.id = c.id_cuenta_origen
        where c.id_estado between :est1 and :est2
        order by c.fecha_pago desc")
        ->bindValue(':caj1', $caja1)
        ->bindValue(':caj2', $caja2)
        ->bindValue(':bco1', $bco1)
        ->bindValue(':bco2', $bco2)
        ->bindValue(':per1', $per1)
        ->bindValue(':per2', $per2)
        ->bindValue(':tip1', $tip1)
        ->bindValue(':tip2', $tip2)
        ->bindValue(':est1', $est1)
        ->bindValue(':est2', $est2)
        ->queryAll();

        return $this->renderPartial('cheque-lista',['listado' => $listado]);
    }

    public function actionChequeGuarda($caja, $banco, $tipo, $persona,
    $importe, $numero, $obs, $librador, $pago, $formato, $orden, $cuenta,$fromMov = 0)
    {
        $db = Yii::$app->db;

        $salida = 0;
        $estadoInicial = 1;
        if ($tipo == 2) {$estadoInicial = 5;}

        $transaction = \Yii::$app->db->beginTransaction();

            $db->createCommand("insert into cheque (nro_cheque, librador, id_persona, id_banco, fecha_pago,
            obs, id_tipo, importe, id_caja, id_usuario, id_estado, orden, electronico, id_cuenta_origen) 
            values(:nro_cheque, :librador, :id_persona, :id_banco, :fecha_pago,
            :obs, :id_tipo, :importe, :id_caja, :id_usuario, :estado, :orden, :formato, :id_cuenta_origen)")
            ->bindValue(':nro_cheque', $numero)
            ->bindValue(':librador', $librador)
            ->bindValue(':id_persona', $persona)
            ->bindValue(':id_banco', $banco)
            ->bindValue(':fecha_pago', $pago)
            ->bindValue(':obs', $obs)
            ->bindValue(':id_tipo', $tipo)
            ->bindValue(':importe', $importe)
            ->bindValue(':id_caja', $caja)
            ->bindValue(':orden', $orden)
            ->bindValue(':formato', $formato)
            ->bindValue(':estado', $estadoInicial)
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->bindValue(':id_cuenta_origen', $cuenta)
            ->execute();

            $ultimoId = $db->getLastInsertID();

            $db->createCommand("insert into cheque_estado_detalle (id_cheque, id_estado, fecha, id_usuario) 
            values(:id_cheque, :id_estado, now(), :id_usuario)")
            ->bindValue(':id_cheque', $ultimoId)
            ->bindValue(':id_estado', $estadoInicial)
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->execute();

            $salida = 1;

        $transaction->commit();

        return $this->renderPartial('cheque-guarda',['salida' =>$salida, 'fromMov' => $fromMov]);
    }

    public function actionMovimientoMedio($medio = 0, $importe = 0, $cheque = 0, $cuenta = 0, $credito = 0, $debito = 0, $guarda = 0)
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;

        if ((int)$guarda == 1) {
            $continuar = 1;

            if ($medio == 5) {     
            // Verificar si el cheque ya está cargado en movimiento_carga
            $chequeCargado = $db->createCommand("select count(*) as total 
                from movimiento_carga 
                where id_medio = 5 and id_cheque = :cheque")
                ->bindValue(':cheque', $cheque)
                ->queryOne();

            if ($chequeCargado['total'] > 0) {
                $continuar = 0;
            }
            }

            if ($continuar == 1) {
                $tarjeta = $credito;
                if ($medio == 3) {$tarjeta = $debito;}
                if ($medio != 5) {$cheque = 0;}
                $db->createCommand("insert into movimiento_carga (id_usuario, id_medio, importe,id_cheque, id_cuenta, id_tarjeta) 
                values(:id_usuario, :id_medio, :importe,:id_cheque, :id_cuenta, :id_tarjeta)")
                ->bindValue(':id_usuario', Yii::$app->user->identity->id)
                ->bindValue(':id_medio', $medio)
                ->bindValue(':importe', $importe)
                ->bindValue(':id_cheque', $cheque)
                ->bindValue(':id_cuenta', $cuenta)
                ->bindValue(':id_tarjeta', $tarjeta)
                ->execute();
            }
        }        

        if ($request->get('eliminar') == 1) {
            $id = $request->get('id');
            $db->createCommand("delete from movimiento_carga where id = :id")
            ->bindValue(':id', $id)
            ->execute();
        }
        
        $listado = $db->createCommand("select mm.*, m.medio, c.nro_cheque, t.tarjeta, cu.cuenta
        from movimiento_carga mm 
        join medio_pago m on m.id = mm.id_medio 
        left join cheque c on c.id = mm.id_cheque
        left join tarjeta t on t.id = mm.id_tarjeta
        left join banco_cuenta cu on cu.id = mm.id_cuenta
        where mm.id_usuario = :id")
        ->bindValue(':id', Yii::$app->user->identity->id)
        ->queryAll();

        return $this->renderPartial('movimiento-medio',['listado' =>$listado]);
    }
    public function actionMovimientoMedioLista($idMov)
    {
        $db = Yii::$app->db;        

        $listado = $db->createCommand("select mm.*, m.medio, c.nro_cheque, t.tarjeta, 
        cu.cuenta, ba.banco, ba1.banco banco_cta
        from movimiento_medio mm 
        join medio_pago m on m.id = mm.id_medio 
        left join cheque c on c.id = mm.id_cheque
        left join banco ba on ba.id = c.id_banco
        left join tarjeta t on t.id = mm.id_tarjeta
        left join banco_cuenta cu on cu.id = mm.id_cuenta
        left join banco ba1 on ba1.id = cu.id_banco
        where mm.id_movimiento = :id")
        ->bindValue(':id', $idMov)
        ->queryAll();

        return $this->renderPartial('movimiento-medio-lista',['listado' =>$listado]);
    }

    public function actionMovimientoImprime($id)
    {
        $db = Yii::$app->db;

        /*$datos = $db->createCommand("select m.*,c.concepto, concat(u.apellido,' ',u.nombre) usuario,
        c.id_tipo tipoConcepto, concat(p.apellido,' ',p.nombre) nombrePersona, p.domicilio,
        l.localidad, p.cuit, ft.tipo_afip, ft.nota_afip
        from movimiento m join persona p on p.id = m.id_persona
        join concepto c on c.id = m.id_concepto
        join user u on u.id = m.id_usuario
        join factura_tipo ft on ft.id = m.id_factura
        left join localidades l on l.idlocalidad = p.id_localidad
        where m.id = :id;")
        ->bindValue(':id', $id)
        ->queryOne();

        if ($datos['cae'] == '' or $datos['cae'] == null) {
            $resultadoCae = self::getCaeDirecto($id, $datos['tipo_afip']);
            
            // Si hay error temporal de AFIP, mostrar mensaje amigable
            if (is_array($resultadoCae) && isset($resultadoCae['error']) && $resultadoCae['mostrar_amigable']) {
                Yii::$app->session->setFlash('warning', $resultadoCae['mensaje']);
            }
        }*/

        $datos = $db->createCommand("select m.*,c.concepto, concat(u.apellido,' ',u.nombre) usuario,
        c.id_tipo tipoConcepto, concat(p.apellido,' ',p.nombre) nombrePersona, p.domicilio,
        l.localidad, p.cuit, ft.tipo_afip, ft.nota_afip
        from movimiento m join persona p on p.id = m.id_persona
        join concepto c on c.id = m.id_concepto
        join user u on u.id = m.id_usuario
        join factura_tipo ft on ft.id = m.id_factura
        left join localidades l on l.idlocalidad = p.id_localidad
        where m.id = :id;")
        ->bindValue(':id', $id)
        ->queryOne();

        $tipoConcepto = $datos['tipoConcepto'];

        $medios = $db->createCommand("select mm.*, m.medio /*, c.nro_cheque, t.tarjeta, 
        cu.cuenta, ba.banco, ba1.banco banco_cta, ct.tipo*/
        from movimiento_medio mm 
        join medio_pago m on m.id = mm.id_medio 
        /*left join cheque c on c.id = mm.id_cheque
        left join banco ba on ba.id = c.id_banco
        left join tarjeta t on t.id = mm.id_tarjeta
        left join banco_cuenta cu on cu.id = mm.id_cuenta
        left join banco ba1 on ba1.id = cu.id_banco
        left join cheque_tipo ct on ct.id = c.id_tipo*/
        where mm.id_movimiento = :id order by m.orden")
        ->bindValue(':id', $id)
        ->queryAll();

        require_once('../vendor/tcpdf/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Comprobante');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10, false);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetFont('helvetica', '', 10);

        $tbl = $this->renderPartial('_recibo', ['datos' => $datos, 'medios' => $medios, 'tipoConcepto' => $tipoConcepto]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->SetFont('helvetica', '', 2);

        if ($datos['cae'] != null and $datos['cae'] != '') {
            $pdf->write1DBarcode($datos['codigobarra'], 'C128', 53, 246, 100, 12, 0.4, array('border' => false), 'N');
        }

        ob_end_clean();
        $pdf->Output('comprobante_'.$id.'.pdf', 'I');
    }

    public static function numeroToLetra ($numero) {
        $db = Yii::$app->db;

        $letra = $db->createCommand("select fn_numeroLetras(:numero) as salida;")
            ->bindValue(':numero', floatval($numero))
            ->queryOne();

        return $letra['salida'];
    }

    public function actionMovimientoCheque($concepto) {
        $db = Yii::$app->db;

        $infoConcepto = $db->createCommand("select * from concepto where id = :id")
        ->bindValue(':id', $concepto)
        ->queryOne();

        if ($infoConcepto['id_tipo'] == 1) {
            $cheques = $db->createCommand("select c.*, b.banco, t.tipo 
            from cheque c 
            join banco b on b.id = c.id_banco
            join cheque_tipo t on t.id = c.id_tipo
            where c.id_estado in (5) and c.id_tipo = 2 order by banco, nro_cheque")
            ->queryAll();
        } else {
            $cheques = $db->createCommand("select c.*, b.banco, t.tipo 
            from cheque c 
            join banco b on b.id = c.id_banco
            join cheque_tipo t on t.id = c.id_tipo
            where c.id_estado in (1,6) order by banco, nro_cheque")
            ->queryAll();
        }

        return $this->renderPartial('movimiento-cheque',['cheques' =>$cheques]);

    }

    public function actionMovimientoPersona($concepto) {
        $db = Yii::$app->db;
        if ($concepto == 3) {
            $personas = $db->createCommand("select id,concat(apellido,' ',nombre) persona from persona where id_tipo_persona in (2) order by apellido, nombre")->queryAll();
        } else {
            if ($concepto == 1 || $concepto == 2) {
                $personas = $db->createCommand("select id,concat(apellido,' ',nombre) persona from persona where id_tipo_persona = 1 order by apellido, nombre")->queryAll();
            } else {
                $personas = $db->createCommand("select id,concat(apellido,' ',nombre) persona from persona order by apellido, nombre")->queryAll();
            }
        }
        return $this->renderPartial('movimiento-persona',['personas' =>$personas]);

    }

    public function actionFactura() {
        $db = Yii::$app->db;
        $facturas = $db->createCommand("select * from factura_tipo where id > 0")->queryAll();
        $sucursales = Sucursal::find()->orderBy("sucursal")->all();

        return $this->render('factura',['tipoFactura' => $facturas, 'sucursales' => $sucursales]);
    }

    public function actionFacturaPersona($tipo) {
        $db = Yii::$app->db;
        $personas = $db->createCommand("select id,concat(apellido,' ',nombre) persona from persona where id_tipo_persona = :tipo order by apellido, nombre")
        ->bindValue(':tipo', $tipo)
        ->queryAll();

        return $this->renderPartial('factura-persona',['personas' =>$personas]);
    }

    public function actionFacturaLista($id, $sucursal,$addMov = 0) {
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $guardar = 0;

        if ($request->post('agregar') == 1) {
            $punto = $request->post('punto');
            $numero = $request->post('numero');
            $fecha = $request->post('fecha');
            $importe = $request->post('importe');
            $obs = $request->post('obs');
            $tipo = $request->post('tipo');
            $sucursal = $request->post('sucursal');
            
            // Procesar archivo si se subió uno
            $nombreArchivo = null;
            $archivoFactura = \yii\web\UploadedFile::getInstanceByName('archivo_factura');
            
            if ($archivoFactura) {
                // Crear directorio si no existe
                $directorioFacturas = Yii::getAlias('@webroot/facturas');
                if (!is_dir($directorioFacturas)) {
                    mkdir($directorioFacturas, 0755, true);
                }
                
                // Generar nombre único para el archivo
                $extension = $archivoFactura->extension;
                $nombreArchivo = 'factura_' . $id . '_' . $punto . '_' . $numero . '_' . time() . '.' . $extension;
                $rutaCompleta = $directorioFacturas . '/' . $nombreArchivo;
                
                // Validar tipo de archivo
                $tiposPermitidos = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
                if (!in_array(strtolower($extension), $tiposPermitidos)) {
                    throw new \yii\web\BadRequestHttpException('Tipo de archivo no permitido. Use PDF, JPG, PNG o GIF.');
                }
                
                // Validar tamaño (5MB máximo)
                if ($archivoFactura->size > 5 * 1024 * 1024) {
                    throw new \yii\web\BadRequestHttpException('El archivo no puede superar los 5MB.');
                }
                
                // Guardar archivo
                if (!$archivoFactura->saveAs($rutaCompleta)) {
                    throw new \yii\web\ServerErrorHttpException('Error al guardar el archivo.');
                }
            }
            
            $db->createCommand("insert into factura (id_persona, id_punto, numero, fecha, importe, obs, id_usuario, creado, id_tipo, id_sucursal, archivo_factura) 
            values(:id_persona, :punto, :numero, :fecha, :importe, :obs, :id_usuario, now(), :tipo, :sucursal, :archivo)")
            ->bindValue(':id_persona', $id)
            ->bindValue(':punto', $punto)
            ->bindValue(':numero', $numero)
            ->bindValue(':fecha', $fecha)
            ->bindValue(':importe', $importe)
            ->bindValue(':tipo', $tipo)
            ->bindValue(':sucursal', $sucursal)
            ->bindValue(':archivo', $nombreArchivo)
            ->bindValue(':obs', $obs)
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->execute();
            $guardar = 1;
        }

        if ($request->get('eliminar') == 1) {
            $idFactura = $request->get('idFactura');
            
            // Obtener información del archivo antes de eliminar
            $factura = $db->createCommand("select archivo_factura from factura where id = :id")
                ->bindValue(':id', $idFactura)
                ->queryOne();
            
            // Eliminar archivo físico si existe
            if ($factura && !empty($factura['archivo_factura'])) {
                $rutaArchivo = Yii::getAlias('@webroot/facturas/') . $factura['archivo_factura'];
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }
            
            // Eliminar registro de la base de datos
            $db->createCommand("delete from factura where id = :id")
            ->bindValue(':id', $idFactura)
            ->execute();
        }

        $suc1 = $sucursal; $suc2 = $sucursal;
        if ($sucursal == 0) {$suc1 = 1; $suc2 = 10;}

        $facturas = $db->createCommand("select f.*, concat(u.apellido,' ',u.nombre) usuario, c.id carga, mf.id movimiento, t.tipo, s.sucursal
        from factura f
        join user u on u.id = f.id_usuario
        left join factura_carga c on c.id_factura = f.id and c.id_usuario = :id_usuario
        left join movimiento_factura mf on mf.id_factura = f.id 
        left join factura_tipo t on t.id = f.id_tipo
        left join sucursal s on s.id = f.id_sucursal
        where id_persona = :id and f.id_sucursal between :suc1 and :suc2 order by fecha desc")
        ->bindValue(':id', $id)
        ->bindValue(':id_usuario', Yii::$app->user->identity->id)
        ->bindValue(':suc1', $suc1)
        ->bindValue(':suc2', $suc2)
        ->queryAll();

        return $this->renderPartial('factura-lista',['facturas' =>$facturas, 'addMov' => $addMov, 'guardar' => $guardar]);
    }

    public function actionMovimientoCargaFactura($id, $valor, $carga = 0) {
        $db = Yii::$app->db;
        if ($carga == 1) {
            if ($valor == 1) {
                // Verificar si ya existe la combinación de usuario y factura
                $existe = $db->createCommand("select count(*) from factura_carga where id_usuario = :id_usuario and id_factura = :id_factura")
                    ->bindValue(':id_usuario', Yii::$app->user->identity->id)
                    ->bindValue(':id_factura', $id)
                    ->queryScalar();
                
                // Solo hacer el insert si no existe
                if ($existe == 0) {
                    $db->createCommand("insert into factura_carga (id_usuario, id_factura) values(:id_usuario, :id_factura)")
                    ->bindValue(':id_usuario', Yii::$app->user->identity->id)
                    ->bindValue(':id_factura', $id)
                    ->execute();
                }
            } else {
                $db->createCommand("delete from factura_carga where id_factura = :id and id_usuario = :id_usuario")
                ->bindValue(':id', $id)
                ->bindValue(':id_usuario', Yii::$app->user->identity->id)
                ->execute();
            }
        }
        $total = $db->createCommand("select count(f.id) cantidad, sum(f.importe) importe
        from factura_carga fc
        join factura f on f.id = fc.id_factura
        where fc.id_usuario = :id_usuario")
        ->bindValue(':id_usuario', Yii::$app->user->identity->id)
        ->queryOne();

        return $this->renderPartial('movimiento-carga-factura',['total' =>$total]);

    }

    public static function guardarCotizacion() {
        //$url = 'https://dolarapi.com/v1/dolares/oficial';
        $url = 'https://dolarapi.com/v1/dolares/blue';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $db = Yii::$app->db;

        $iso = $data['fechaActualizacion'];
        $date = new DateTime($iso, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
        $mysqlFormat = $date->format('Y-m-d H:i:s');        

        // Verificar si ya existe un registro con esta fecha de actualización
        $existe = $db->createCommand("select count(*) from cotizacion_dolar where ultima_actualizacion = :ultima_actualizacion")
            ->bindValue(':ultima_actualizacion', $mysqlFormat)
            ->queryScalar();
        
        // Solo insertar si no existe
        if ($existe == 0) {
            $db->createCommand("insert into cotizacion_dolar (compra, venta, ultima_actualizacion) 
            values(:compra, :venta, :ultima_actualizacion)")
            ->bindValue(':compra', $data['compra'])
            ->bindValue(':venta', $data['venta'])
            ->bindValue(':ultima_actualizacion', $mysqlFormat)
            ->execute();
        }
    }

    public static function getCotizacion() {
        $db = Yii::$app->db;

        $cotizacion = $db->createCommand("select * from cotizacion_dolar order by id desc limit 1;")->queryOne();

        return $cotizacion['venta'];
    }

    public function actionMovimientoListaImprime($concepto, $persona, $desde, $hasta, $caja)
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $idMov = $request->get('idMov');

        $caja1 = $caja; $caja2 = $caja;
        if ($caja == 0) {$caja1 = 1; $caja2 = 10;}
        $con1 = $concepto; $con2 = $concepto;
        if ($concepto == 0) {$con1 = 1; $con2 = 10000;}
        $per1 = $persona; $per2 = $persona;
        if ($persona == 0) {$per1 = 0; $per2 = 10000;}

        $listado = $db->createCommand("select m.*,p.id,m.id idMov,concat(p.apellido,' ', p.nombre) persona, c.concepto, ca.empresa,
        f.tipo tipo_factura, concat(u.apellido,' ',u.nombre) usuario
        from movimiento m         
        join concepto c on c.id = m.id_concepto
join sueldosempresas ca on ca.idEmpresa = m.id_empresa        join user u on u.id = m.id_usuario
        left join factura_tipo f on f.id = m.id_factura
        left join persona p on p.id = m.id_persona
        where m.id_empresa between :caj1 and :caj2 and m.id_concepto between :con1 and :con2 
        and m.id_persona between :per1 and :per2 and m.fecha between :desde and :hasta 
        order by m.fecha desc, m.hora desc")
        ->bindValue(':caj1', $caja1)
        ->bindValue(':caj2', $caja2)
        ->bindValue(':con1', $con1)
        ->bindValue(':con2', $con2)
        ->bindValue(':per1', $per1)
        ->bindValue(':per2', $per2)
        ->bindValue(':desde', $desde)
        ->bindValue(':hasta', $hasta)
        ->queryAll();

        require_once('../vendor/tcpdf/tcpdf.php');        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Comprobante');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10, false);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('L', 'A4');
        $tbl = $this->renderPartial('_movimiento_lista', ['listado' => $listado, 'desde' => $desde, 'hasta' => $hasta]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        ob_end_clean();
        $pdf->Output('movimiento_lista.pdf', 'I');
    }

    public function actionChequeListaImprime($caja, $banco, $tipo, $persona, $estado, $desde = null, $hasta = null)
    {
        $db = Yii::$app->db;

        $caja1 = $caja; $caja2 = $caja;
        if ($caja == 0) {$caja1 = 1; $caja2 = 10;}
        $bco1 = $banco; $bco2 = $banco;
        if ($banco == 0) {$bco1 = 1; $bco2 = 10000;}
        $per1 = $persona; $per2 = $persona;
        if ($persona == 0) {$per1 = 0; $per2 = 10000;}
        $tip1 = $tipo; $tip2 = $tipo;
        if ($tipo == 0) {$tip1 = 0; $tip2 = 10000;}
        $est1 = $estado; $est2 = $estado;
        if ($estado == 0) {$est1 = 0; $est2 = 100;}

        if ($desde != null && $hasta != null) {
            $listado = $db->createCommand("
            select c.*, t.tipo, ca.caja, concat(p.apellido,' ',p.nombre) persona, b.banco,
            concat(u.apellido,' ',u.nombre) usuario, e.estado, DATE_ADD(c.fecha_pago, INTERVAL 1 month) fecha_vto,
            ce.formato, co.tipo ordenNombre
            from cheque c
            join cheque_tipo t on t.id = c.id_tipo
            join caja ca on ca.id = c.id_caja
            left join persona p on p.id = c.id_persona
            join banco b on b.id = c.id_banco
            join cheque_estado e on e.id = c.id_estado
            join user u on u.id = c.id_usuario
            left join cheque_electronico ce on ce.id = c.electronico
            left join cheque_orden co on co.id = c.orden
            where c.id_tipo = 1 and c.fecha_pago between :desde and :hasta
            order by c.fecha_pago desc")
            ->bindValue(':desde', $desde)
            ->bindValue(':hasta', $hasta)
            ->queryAll();
        } else {
            $listado = $db->createCommand("
            select c.*, t.tipo, ca.caja, concat(p.apellido,' ',p.nombre) persona, b.banco,
            concat(u.apellido,' ',u.nombre) usuario, e.estado, DATE_ADD(c.fecha_pago, INTERVAL 1 month) fecha_vto,
            ce.formato, co.tipo ordenNombre
            from cheque c
            join cheque_tipo t on t.id = c.id_tipo
            join caja ca on ca.id = c.id_caja
            left join persona p on p.id = c.id_persona
            join banco b on b.id = c.id_banco
            join cheque_estado e on e.id = c.id_estado
            join user u on u.id = c.id_usuario
            left join cheque_electronico ce on ce.id = c.electronico
            left join cheque_orden co on co.id = c.orden
            where c.id_caja between :caj1 and :caj2 and c.id_banco between :bco1 and :bco2 
            and c.id_persona between :per1 and :per2 and c.id_tipo between :tip1 and :tip2 
            and c.id_estado between :est1 and :est2
            order by c.fecha_pago desc")
            ->bindValue(':caj1', $caja1)
            ->bindValue(':caj2', $caja2)
            ->bindValue(':bco1', $bco1)
            ->bindValue(':bco2', $bco2)
            ->bindValue(':per1', $per1)
            ->bindValue(':per2', $per2)
            ->bindValue(':tip1', $tip1)
            ->bindValue(':tip2', $tip2)
            ->bindValue(':est1', $est1)
            ->bindValue(':est2', $est2)
            ->queryAll();
        }

        require_once('../vendor/TCPDF-main/tcpdf.php');
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Cheques');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10, false);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('L', 'A4');
        $tbl = $this->renderPartial('_cheque_lista', ['listado' => $listado]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        ob_end_clean();
        $pdf->Output('cheque_lista.pdf', 'I');
    }

    public function actionChequeCambio($idCheque)
    {
        $db = Yii::$app->db;
        $estados = null;
        $infoCheque = $db->createCommand("select * from cheque where id = :id")->bindValue(':id', $idCheque)->queryOne();
        if ($infoCheque['id_tipo'] == 2) {
            if ($infoCheque['id_estado'] == 6) {
                $estados = $db->createCommand("select * from cheque_estado where id > 0 and id = 7")->queryAll();
            }
            if ($infoCheque['id_estado'] == 7) {
                $estados = $db->createCommand("select * from cheque_estado where id > 0 and id in (8, 10)")->queryAll();
            }
        }
        
        $cuentas = $db->createCommand( "select * from banco_cuenta")->queryAll();

        return $this->renderPartial('cheque-cambio', ['estados' => $estados, 'cuentas' => $cuentas, 'idCheque' => $idCheque]);
    }

    public function actionSaldo()
    {        
        $db = Yii::$app->db;
        $cajas = $db->createCommand("select * from caja")->queryAll();
        $medios = $db->createCommand("select * from medio_pago order by orden")->queryAll();
        $cuentas = $db->createCommand("select * from banco_cuenta")->queryAll();

        return $this->render('saldo',['cajas' => $cajas, 'medios' => $medios, 'cuentas' => $cuentas]);
    }    

    public function actionSaldoLista($caja, $medio, $desde, $hasta, $cuenta = 0)
    {        
        $db = Yii::$app->db;
        $listado = $db->createCommand("call saldoCaja(:caja, :medio, :desde, :hasta, :cuenta)")
        ->bindValue(':caja', $caja)
        ->bindValue(':medio', $medio)
        ->bindValue(':desde', $desde)
        ->bindValue(':hasta', $hasta)
        ->bindValue(':cuenta', $cuenta)
        ->queryAll();

        return $this->renderPartial('saldo-lista',['listado' => $listado, 'medio' => $medio, 'desde' => $desde, 'hasta' => $hasta]);
    }    

    public function actionSaldoListaImprime($caja, $medio, $desde, $hasta, $cuenta = 0)
    {        
        $db = Yii::$app->db;
        $listado = $db->createCommand("call saldoCaja(:caja, :medio, :desde, :hasta, :cuenta)")
        ->bindValue(':caja', $caja)
        ->bindValue(':medio', $medio)
        ->bindValue(':desde', $desde)
        ->bindValue(':hasta', $hasta)
        ->bindValue(':cuenta', $cuenta)
        ->queryAll();

        require_once('../vendor/TCPDF-main/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Saldos');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10, false);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('P', 'A4');
        $tbl = $this->renderPartial('_saldo_lista', ['listado' => $listado, 'desde' => $desde, 'hasta' => $hasta, 'medio' => $medio]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        ob_end_clean();
        $pdf->Output('saldo_lista.pdf', 'I');
    }    

    public function actionChequeInfo($idCheque)
    {
        $db = Yii::$app->db;
        $infoCheque = $db->createCommand("select c.*, t.tipo, ca.empresa, concat(p.apellido,' ',p.nombre) persona, b.banco,
        concat(u.apellido,' ',u.nombre) usuario, e.estado, DATE_ADD(c.fecha_pago, INTERVAL 1 month) fecha_vto,
        ce.formato, co.tipo ordenNombre, max(endo.proveedor) proveedor, bc.cuenta
        from cheque c
        join cheque_tipo t on t.id = c.id_tipo
        join sueldosempresas ca on ca.IdEmpresa = c.id_caja
        left join persona p on p.id = c.id_persona
        join banco b on b.id = c.id_banco
        join cheque_estado e on e.id = c.id_estado
        join user u on u.id = c.id_usuario
        left join cheque_electronico ce on ce.id = c.electronico
        left join cheque_orden co on co.id = c.orden
        left join (select mm.id_cheque, concat(p.apellido,' ',p.nombre) proveedor 
        from movimiento_medio mm 
        left join movimiento m on m.id = mm.id_movimiento
        left join concepto c2 on c2.id = m.id_concepto 
        left join persona p on p.id = m.id_persona
        where c2.id_tipo = 2 and m.estado = 1 group by mm.id_cheque, p.apellido,p.nombre) endo on endo.id_cheque = c.id
        left join banco_cuenta bc on bc.id = c.id_cuenta
        where c.id = :id
        group by c.id, p.apellido,p.nombre")
        ->bindValue(':id', $idCheque)
        ->queryOne();

        return $this->renderPartial('cheque-info', ['infoCheque' => $infoCheque]);
    }

    public function actionChequePropio()
    {        
        $db = Yii::$app->db;
        return $this->render('cheque-propio');
    }

    public function actionChequePropioSemana($fechaDesde, $fechaHasta)
    {
        $db = Yii::$app->db;
        
        // Consulta para obtener cheques propios en el rango de fechas
        $listado = $db->createCommand("
            select c.*, b.banco, ca.caja, concat(p.apellido,' ',p.nombre) persona, 
                   e.estado, DATE_ADD(c.fecha_pago, INTERVAL 1 month) fecha_vto,
                   ce.formato, co.tipo ordenNombre, c.electronico, c.nro_interno,
                   concat(u.apellido,' ',u.nombre) usuario
            from cheque c
            join banco b on b.id = c.id_banco
            join caja ca on ca.id = c.id_caja
            left join persona p on p.id = c.id_persona
            join cheque_estado e on e.id = c.id_estado
            join user u on u.id = c.id_usuario
            left join cheque_electronico ce on ce.id = c.electronico
            left join cheque_orden co on co.id = c.orden
            where c.id_tipo = 1 
            and c.fecha_pago between :fechaDesde and :fechaHasta
            order by c.fecha_pago desc, c.id desc
        ")
        ->bindValue(':fechaDesde', $fechaDesde)
        ->bindValue(':fechaHasta', $fechaHasta)
        ->queryAll();

        return $this->renderPartial('cheque-propio-lista', [
            'listado' => $listado,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta
        ]);
    }

    public function actionChequePropioSemanaData($fechaDesde, $fechaHasta)
    {
        $db = Yii::$app->db;
        
        // Obtener datos de resumen para la semana
        $resumen = $db->createCommand("
            select count(*) as total_cheques, 
                   COALESCE(sum(c.importe), 0) as total_importe
            from cheque c
            where c.id_tipo = 1 
            and c.fecha_pago between :fechaDesde and :fechaHasta
        ")
        ->bindValue(':fechaDesde', $fechaDesde)
        ->bindValue(':fechaHasta', $fechaHasta)
        ->queryOne();

        // Obtener el listado completo
        $listado = $db->createCommand("
            select c.*, b.banco, ca.caja, concat(p.apellido,' ',p.nombre) persona, 
                   e.estado, DATE_ADD(c.fecha_pago, INTERVAL 1 month) fecha_vto,
                   ce.formato, co.tipo ordenNombre, c.electronico, c.nro_interno,
                   concat(u.apellido,' ',u.nombre) usuario
            from cheque c
            join banco b on b.id = c.id_banco
            join caja ca on ca.id = c.id_caja
            left join persona p on p.id = c.id_persona
            join cheque_estado e on e.id = c.id_estado
            join user u on u.id = c.id_usuario
            left join cheque_electronico ce on ce.id = c.electronico
            left join cheque_orden co on co.id = c.orden
            where c.id_tipo = 1 
            and c.fecha_pago between :fechaDesde and :fechaHasta
            order by c.fecha_pago desc, c.id desc
        ")
        ->bindValue(':fechaDesde', $fechaDesde)
        ->bindValue(':fechaHasta', $fechaHasta)
        ->queryAll();

        // Retornar JSON con los datos
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'success' => true,
            'resumen' => $resumen,
            'listado' => $listado,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta
        ];
    }

    public function actionChequePropioSemanasConDatos($fechaDesde, $fechaHasta)
    {
        $db = Yii::$app->db;
        
        // Obtener todas las semanas que tienen cheques en el rango
        $semanas = $db->createCommand("
            select DISTINCT CONCAT(YEAR(c.fecha_pago), '-W', LPAD(WEEK(c.fecha_pago, 1), 2, '0')) as semana
            from cheque c
            where c.id_tipo = 1 
            and c.fecha_pago between :fechaDesde and :fechaHasta
            order by c.fecha_pago
        ")
        ->bindValue(':fechaDesde', $fechaDesde)
        ->bindValue(':fechaHasta', $fechaHasta)
        ->queryAll();

        $semanasArray = array_column($semanas, 'semana');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'success' => true,
            'semanas' => $semanasArray
        ];
    }

    public function actionDeposito()
    {        
        $db = Yii::$app->db;
        $cajas = $db->createCommand("select * from caja")->queryAll();
        $medios = $db->createCommand("select * from medio_pago where id in (1,9) order by orden")->queryAll();
        $cuentas = $db->createCommand("select * from banco_cuenta")->queryAll();

        return $this->render('deposito',['cajas' => $cajas, 'medios' => $medios, 'cuentas' => $cuentas]);
    }    

    public function actionDepositoCarga()
    {        
        $db = Yii::$app->db;
        $cajas = $db->createCommand("select * from caja")->queryAll();
        $medios = $db->createCommand("select * from medio_pago where id in (1,9) order by orden")->queryAll();
        $cuentas = $db->createCommand("select * from banco_cuenta")->queryAll();

        return $this->renderPartial('deposito-carga',['cajas' => $cajas, 'medios' => $medios, 'cuentas' => $cuentas]);
    }    

    public function actionDepositoLista($caja, $medio, $cuenta)
    {        
        $db = Yii::$app->db;

        $request = Yii::$app->request;
        $depositar = $request->get('depositar');

        if ($depositar == 1) {
            $sucursal_carga = $request->get('sucursal_carga');
            $medio_carga = $request->get('medio_carga');
            $cuenta_carga = $request->get('cuenta_carga');
            $importe_carga = $request->get('importe_carga');
            $usuario = Yii::$app->user->identity->id;

            $transaction = \Yii::$app->db->beginTransaction();
            $db->createCommand("insert into deposito_cuenta (id_sucursal, id_medio, id_cuenta, importe, id_usuario, fecha, hora, estado) 
            values (:sucursal, :medio, :cuenta, :importe, :usuario, curdate(), curtime(), 1)")
            ->bindValue(':sucursal', $sucursal_carga)
            ->bindValue(':medio', $medio_carga)
            ->bindValue(':cuenta', $cuenta_carga)
            ->bindValue(':importe', $importe_carga)
            ->bindValue(':usuario', $usuario)
            ->execute();
            
            $ultDep = $db->createCommand("select max(id) ultId from deposito_cuenta")->queryOne();
            $idDep = $ultDep["ultId"];

            $cotizacion = self::getCotizacion();
            $importeDolar = floatval($importe_carga / $cotizacion);

            $db->createCommand("insert into movimiento (id_caja,fecha, hora, id_concepto, id_persona, id_usuario, importe, cotizacion_dolar, importe_dolar, id_deposito) 
            values(:caja,curdate(), curtime(), 18, 0, :id_usuario, :importe, :cotizacion, :importeDolar, :idDep)")
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->bindValue(':importe', floatval($importe_carga))
            ->bindValue(':caja', $sucursal_carga)
            ->bindValue(':cotizacion', $cotizacion)
            ->bindValue(':importeDolar', $importeDolar)
            ->bindValue(':idDep', $idDep)
            ->execute();  

            $ultMov = $db->createCommand("select max(id) ultId from movimiento")->queryOne();
            $idMov = $ultMov["ultId"];

            $db->createCommand("insert into movimiento_medio (id_movimiento, id_medio, importe,id_cheque, id_cuenta, id_tarjeta)
            values(:idMov, :id_medio, :importe, 0, 0, 0)")
            ->bindValue(':idMov', (int)$idMov)
            ->bindValue(':id_medio', $medio_carga)
            ->bindValue(':importe', floatval($importe_carga))
            ->execute();

            $db->createCommand("insert into movimiento (id_caja,fecha, hora, id_concepto, id_persona, id_usuario, importe, cotizacion_dolar, importe_dolar, id_deposito) 
            values(:caja,curdate(), curtime(), 19, 0, :id_usuario, :importe, :cotizacion, :importeDolar, :idDep)")
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->bindValue(':importe', floatval($importe_carga))
            ->bindValue(':caja', $sucursal_carga)
            ->bindValue(':cotizacion', $cotizacion)
            ->bindValue(':importeDolar', $importeDolar)
            ->bindValue(':idDep', $idDep)
            ->execute();  

            $ultMov = $db->createCommand("select max(id) ultId from movimiento")->queryOne();
            $idMov = $ultMov["ultId"];

            $db->createCommand("insert into movimiento_medio (id_movimiento, id_medio, importe,id_cheque, id_cuenta, id_tarjeta)
            values(:idMov, 4, :importe, 0, :id_cuenta, 0)")
            ->bindValue(':idMov', (int)$idMov)
            ->bindValue(':importe', floatval($importe_carga))
            ->bindValue(':id_cuenta', $cuenta_carga)
            ->execute();

            $transaction->commit();
        }

        if ($request->get('anular') == 1) {
            $id = $request->get('id');
            $db->createCommand("update deposito_cuenta set estado = 0 where id = :id")
            ->bindValue(':id', $id)
            ->execute();

            $db->createCommand("update movimiento set estado = 0 where id_deposito > 0 and id_deposito = :id")
            ->bindValue(':id', $id)
            ->execute();
        }


        $caj1 = $caja; $caj2 = $caja;
        $med1 = $medio; $med2 = $medio;
        $cue1 = $cuenta; $cue2 = $cuenta;
        if ($caja == 0) {
            $caj1 = 1; $caj2 = 9;
        }
        if ($medio == 0) {
            $med1 = 1; $med2 = 9;
        }
        if ($cuenta == 0) {
            $cue1 = 1; $cue2 = 999;
        }

        $listado = $db->createCommand("select d.*, s.sucursal, m.medio, bc.cuenta, concat(u.apellido,' ',u.nombre) usuario
        from deposito_cuenta d
        join sucursal s on s.id = d.id_sucursal
        join medio_pago m on m.id = d.id_medio
        join banco_cuenta bc on bc.id = d.id_cuenta
        join user u on u.id = d.id_usuario
        where d.id_sucursal between :caj1 and :caj2
        and d.id_medio between :med1 and :med2
        and d.id_cuenta between :cue1 and :cue2")
        ->bindValue(':caj1', $caj1)
        ->bindValue(':caj2', $caj2)
        ->bindValue(':med1', $med1)
        ->bindValue(':med2', $med2)
        ->bindValue(':cue1', $cue1)
        ->bindValue(':cue2', $cue2)
        ->queryAll();

        return $this->renderPartial('deposito-lista',['listado' => $listado]);
    }    

    public function actionDepositoListaImprime($caja, $medio, $cuenta)
    {        
        $db = Yii::$app->db;

        $caj1 = $caja; $caj2 = $caja;
        $med1 = $medio; $med2 = $medio;
        $cue1 = $cuenta; $cue2 = $cuenta;
        if ($caja == 0) {
            $caj1 = 1; $caj2 = 9;
        }
        if ($medio == 0) {
            $med1 = 1; $med2 = 9;
        }
        if ($cuenta == 0) {
            $cue1 = 1; $cue2 = 999;
        }

        $listado = $db->createCommand("select d.*, s.sucursal, m.medio, bc.cuenta, concat(u.apellido,' ',u.nombre) usuario
        from deposito_cuenta d
        join sucursal s on s.id = d.id_sucursal
        join medio_pago m on m.id = d.id_medio
        join banco_cuenta bc on bc.id = d.id_cuenta
        join user u on u.id = d.id_usuario
        where d.id_sucursal between :caj1 and :caj2
        and d.id_medio between :med1 and :med2
        and d.id_cuenta between :cue1 and :cue2")
        ->bindValue(':caj1', $caj1)
        ->bindValue(':caj2', $caj2)
        ->bindValue(':med1', $med1)
        ->bindValue(':med2', $med2)
        ->bindValue(':cue1', $cue1)
        ->bindValue(':cue2', $cue2)
        ->queryAll();

        require_once('../vendor/TCPDF-main/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Depósitos');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10, false);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('P', 'A4');
        $tbl = $this->renderPartial('_deposito_lista', ['listado' => $listado]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        ob_end_clean();
        $pdf->Output('deposito_lista.pdf', 'I');
    }    

    public function actionViajeLista($idPersona, $empresa)  
    {
        $db = Yii::$app->db;

        $listado = $db->createCommand("
        select v.*, concat(p.apellido,' ',p.nombre) cliente,
        concat(e1.apellido,' ',e1.nombre) chofer_1,
        concat(e2.apellido,' ',e2.nombre) chofer_2,
        concat(u.apellido,' ',u.nombre) usuario,
        lo.localidad local_origen, ld.localidad local_destino,
        po.provincia pcia_origen, pd.provincia pcia_destino,
        pao.pais pais_origen, pad.pais pais_destino,
        vh.numero_interno coche,
        se.Empresa empresa, pm.importe_pagado
        from viaje v
        join persona p on p.id = v.id_cliente
        left join empleados e1 on e1.idempleado = v.id_chofer_1
        left join empleados e2 on e2.idempleado = v.id_chofer_1
        left join persona p2 on p2.id = v.id_chofer_2
        left join user u on u.id = v.id_usuario
        left join localidades lo on lo.idlocalidad = v.origen
        left join localidades ld on ld.idlocalidad = v.destino
        left join provincia po on po.id = lo.id_provincia
        left join provincia pd on pd.id = ld.id_provincia
        left join pais pao on pao.id = po.id_pais
        left join pais pad on pad.id = po.id_pais
        left join vehiculo vh on vh.id = v.id_vehiculo
        left join sueldosempresas se on se.idEmpresa = v.id_empresa
        left join (select id_viaje, sum(importe) importe_pagado from persona_movimiento where id_movimiento_tipo = 2 group by id_viaje) pm on pm.id_viaje = v.id
        where v.id_cliente = :idPersona and v.id_empresa = :empresa and 
        case when pm.importe_pagado is null then 0 else pm.importe_pagado end < v.total")
        ->bindValue(':idPersona', $idPersona)
        ->bindValue(':empresa', $empresa)
        ->queryAll();

        return $this->renderPartial('viaje-lista', ['listado' => $listado]);
    }

    public static function getCaeDirecto($idOp, $tipoCpbte) {
        $db = Yii::$app->db;   

        $factura = 1;

        $findNota = $db->createCommand("select nota_afip from factura_tipo where nota_afip = :idTipo")
        ->bindValue(':idTipo', $tipoCpbte)
        ->queryOne();

        if ($findNota != null) { $factura = 0; }

        try {
            self::logInfo("Iniciando generación CAE - IdOp: $idOp, Tipo: $tipoCpbte");
            
            // 1. Obtener datos de la operación (una sola llamada al SP)
            $info = $db->createCommand("call afipDatosCae(:idOp);")
            ->bindValue(':idOp', $idOp)
            ->queryOne();
        
            if (!$info) {
                throw new \Exception("No se pudieron obtener los datos de la operación $idOp");
            }

            // 2. Validar y extraer datos requeridos
            $cuit = $info['cuitAfip'] ?? null;
            $punto = $info['puntoventaafip'] ?? null;
            $importe = $info['total'] ?? null;
            $fecha = $info['fecha'] ?? null;
            $nroCpbteCae = $info['NroComprobanteCae'] ?? null;
            $crt_file = $info['crtfile'] ?? null;
            $key_file = $info['keyfile'] ?? null;
            $nroDoc = $info['nrodoc'] ?? null;

            // Validar datos críticos
            if (!$cuit || !$punto || !$importe || !$fecha || !$nroDoc) {
                throw new \Exception("Faltan datos requeridos: CUIT, punto de venta, importe, fecha o número de documento");
            }

            // 3. Configuración del servicio WSFE
            $wsfeUrl = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL'; // Homologación
            //$wsfeUrl = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL'; // Producción

            // Determinar el ambiente (producción vs testing)
            $esProduccion = strpos($wsfeUrl, 'servicios1.afip.gov.ar') !== false;
            $ambiente = $esProduccion ? 'produccion' : 'testing';
            
            // Intentar cargar certificados desde archivos primero
            $certDir = __DIR__ . "/../certificates/afip/$ambiente/";
            $certFile = $certDir . $crt_file;
            $keyFile = $certDir . $key_file;
            
            $crt = null;
            $key = null;
            
            if (file_exists($certFile)) {
                $crt = file_get_contents($certFile);
                if ($crt === false || empty(trim($crt))) {
                    self::logInfo("Advertencia: El archivo de certificado existe pero está vacío: $certFile");
                    $crt = null;
                } else {
                    self::logInfo("Certificado cargado desde archivo: $certFile (tamaño: " . strlen($crt) . " bytes)");
                }
            } else {
                self::logInfo("Archivo de certificado no encontrado: $certFile");
            }
            
            if (file_exists($keyFile)) {
                $key = file_get_contents($keyFile);
                if ($key === false || empty(trim($key))) {
                    self::logInfo("Advertencia: El archivo de clave privada existe pero está vacío: $keyFile");
                    $key = null;
                } else {
                    self::logInfo("Clave privada cargada desde archivo: $keyFile (tamaño: " . strlen($key) . " bytes)");
                }
            } else {
                self::logInfo("Archivo de clave privada no encontrado: $keyFile");
            }
            
            // Si no hay certificados de archivo, intentar cargar desde BD
            if (empty($crt) && !empty($info['crtfile'])) {
                $crt = $info['crtfile'];
                if (!empty(trim($crt))) {
                    self::logInfo("Certificado cargado desde base de datos (tamaño: " . strlen($crt) . " bytes)");
                } else {
                    self::logInfo("Advertencia: El certificado en la base de datos está vacío");
                    $crt = null;
                }
            }
            
            if (empty($key) && !empty($info['keyfile'])) {
                $key = $info['keyfile'];
                if (!empty(trim($key))) {
                    self::logInfo("Clave privada cargada desde base de datos (tamaño: " . strlen($key) . " bytes)");
                } else {
                    self::logInfo("Advertencia: La clave privada en la base de datos está vacía");
                    $key = null;
                }
            }
            
            // Validar que tengamos ambos certificados antes de continuar
            if (empty($crt)) {
                throw new \Exception("No se pudo cargar el certificado. Verifique que el archivo exista en: $certFile o que esté guardado en la base de datos.");
            }
            
            if (empty($key)) {
                throw new \Exception("No se pudo cargar la clave privada. Verifique que el archivo exista en: $keyFile o que esté guardada en la base de datos.");
            }

            $fechaFormateada = date("Ymd", strtotime($fecha));
            self::logInfo("Datos extraídos - CUIT: $cuit, Punto: $punto, Importe: $importe, Fecha: $fechaFormateada");

            // 4. Obtener token y signature
        $salida = self::getTaDirecto($cuit, $crt, $key, $esProduccion);

        if (!isset($salida['token']) || !isset($salida['sign'])) {
            throw new \Exception("Error al obtener token de autenticación de AFIP");
        }

        $token = $salida['token'];
        $sign = $salida['sign'];
            self::logInfo("Token obtenido correctamente");

        $wsdlPath = self::getWsdlPath($esProduccion ? 'wsfev1' : 'wsfev1-homo');
        // Definir el endpoint real del servicio
        $serviceLocation = $esProduccion 
            ? 'https://servicios1.afip.gov.ar/wsfev1/service.asmx'
            : 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx';
        
        // Log información de depuración
        self::logInfo("WSDL Path: $wsdlPath");
        self::logInfo("Service Location: $serviceLocation");
        self::logInfo("Ambiente: " . ($esProduccion ? 'Producción' : 'Homologación'));
        
        // Verificar si el WSDL local existe
        if (file_exists($wsdlPath)) {
            self::logInfo("Usando WSDL local: $wsdlPath");
        } else {
            self::logInfo("WSDL local no encontrado, usando remoto: $wsdlPath");
        }
        
        // Crear contexto de stream con configuración más permisiva
        $contextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT,
                'ciphers' => 'ALL:!ADH:!LOW:!EXP:!MD5:@STRENGTH',
                'disable_compression' => true,
                'SNI_enabled' => true,
            ],
            'http' => [
                'user_agent' => 'Mozilla/5.0 (compatible; PHP SOAP Client)',
                'timeout' => 60,
                'follow_location' => true,
                'max_redirects' => 5,
                'protocol_version' => '1.1',
                'header' => [
                    'Connection: keep-alive',
                    'Accept: text/xml, application/xml, application/soap+xml, text/html, */*',
                    'Accept-Encoding: identity',
                    'Content-Type: text/xml; charset=utf-8'
                ]
            ]
        ];
        
        $streamContext = stream_context_create($contextOptions);
        
        // Verificar conectividad básica usando fsockopen o curl si está disponible
        self::logInfo("Verificando conectividad al servicio...");
        $host = parse_url($serviceLocation, PHP_URL_HOST);
        $port = parse_url($serviceLocation, PHP_URL_PORT) ?: 443;
        
        if ($host) {
            $connection = @fsockopen('ssl://' . $host, $port, $errno, $errstr, 5);
            if ($connection) {
                fclose($connection);
                self::logInfo("Conectividad SSL verificada exitosamente a $host:$port");
            } else {
                self::logError("No se pudo establecer conexión SSL a $host:$port - Error $errno: $errstr");
            }
        }
        
        try {
            $client = new \SoapClient($wsdlPath, [
                'soap_version' => SOAP_1_2,
                'location' => $serviceLocation,
                'uri' => $serviceLocation,
                'trace' => 1,
                'exceptions' => 1,
                'connection_timeout' => 60,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'keep_alive' => true,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                'stream_context' => $streamContext,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                'encoding' => 'UTF-8'
            ]);
            self::logInfo("Cliente SOAP creado exitosamente");
        } catch (\SoapFault $e) {
            $errorMsg = "Error al crear cliente SOAP: " . $e->getMessage();
            self::logError($errorMsg);
            self::logError("Código de error: " . $e->getCode());
            self::logError("Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
            
            // Si el error es de conexión, proporcionar más información
            if (strpos($e->getMessage(), 'Could not connect') !== false || 
                strpos($e->getMessage(), 'Connection refused') !== false ||
                strpos($e->getMessage(), 'SSL') !== false ||
                strpos($e->getMessage(), 'TLS') !== false) {
                throw new \Exception("No se pudo conectar al servicio de AFIP. " .
                    "Verifique:\n" .
                    "1. Conectividad a internet\n" .
                    "2. Firewall/proxy que permita conexiones a *.afip.gov.ar\n" .
                    "3. Que el archivo WSDL local exista en: " . dirname($wsdlPath) . "\n" .
                    "4. URL del servicio: $serviceLocation\n" .
                    "5. Versión de PHP y extensiones SSL/TLS instaladas\n" .
                    "Error original: " . $e->getMessage() . " (Código: " . $e->getCode() . ")");
            }
            throw $e;
        } catch (\Exception $e) {
            $errorMsg = "Error inesperado al crear cliente SOAP: " . $e->getMessage();
            self::logError($errorMsg);
            throw $e;
        }

            // 6. Obtener el último número de comprobante
        $paramsUltimo = [
            'Auth' => [
                'Token' => $token,
                'Sign' => $sign,
                'Cuit' => $cuit
            ],
                'PtoVta' => (int)$punto,
                'CbteTipo' => (int)$tipoCpbte
        ];

        try {
            self::logInfo("Llamando a FECompUltimoAutorizado...");
            $response = $client->FECompUltimoAutorizado($paramsUltimo);
            self::logInfo("Respuesta recibida exitosamente");
        } catch (\SoapFault $e) {
            $errorMsg = "Error en FECompUltimoAutorizado: " . $e->getMessage();
            self::logError($errorMsg);
            
            // Log del request y response si está disponible
            if (isset($client)) {
                try {
                    self::logError("Request: " . $client->__getLastRequest());
                    self::logError("Response: " . $client->__getLastResponse());
                } catch (\Exception $logEx) {
                    // Ignorar errores al obtener el log
                }
            }
            
            // Si el error es de conexión, proporcionar más información
            if (strpos($e->getMessage(), 'Could not connect') !== false || 
                strpos($e->getMessage(), 'Connection refused') !== false ||
                strpos($e->getMessage(), 'Connection timed out') !== false) {
                throw new \Exception("No se pudo conectar al servicio de AFIP. " .
                    "Verifique:\n" .
                    "1. Conectividad a internet\n" .
                    "2. Firewall/proxy que permita conexiones HTTPS a *.afip.gov.ar (puerto 443)\n" .
                    "3. Que el archivo WSDL local exista en: " . dirname($wsdlPath) . "\n" .
                    "4. URL del servicio: $serviceLocation\n" .
                    "5. WSDL usado: $wsdlPath\n" .
                    "Error original: " . $e->getMessage());
            }
            throw $e;
        }
            
        if (!isset($response->FECompUltimoAutorizadoResult->CbteNro)) {
            throw new \Exception("Error al obtener último número de comprobante para punto $punto tipo $tipoCpbte");
        }
            
        $ultimo = $response->FECompUltimoAutorizadoResult->CbteNro;
        $proximo = $ultimo + 1;
        self::logInfo("Último comprobante: $ultimo, Próximo: $proximo");

        // 7. Armar la factura a emitir según el tipo
        $datos = null;
            
        if ($factura == 1) {
            $importeTotal = (float)$importe;
            $importeNeto = (float)$importe;
            $importeIVA = 0;

            if ((int)$tipoCpbte == 1 or (int)$tipoCpbte == 6) {
                $importeTotal = number_format((float)$importe, 2, '.', '');
                $importeNeto = number_format((float)$importe /1.21, 2, '.', '');
                $importeIVA = number_format((float)($importe - $importeNeto), 2, '.', '');
            }

            $datos = [
                'Auth' => [
                    'Token' => $token,
                    'Sign' => $sign,
                    'Cuit' => $cuit
                ],
                'FeCAEReq' => [
                    'FeCabReq' => [
                        'CantReg' => 1,
                            'PtoVta' => (int)$punto,
                            'CbteTipo' => (int)$tipoCpbte
                    ],
                    'FeDetReq' => [
                        'FECAEDetRequest' => [
                            "Concepto" => 2,
                            "DocTipo" => 80, // 96 = CUIT | 80 = DNI
                            "DocNro" => (int)$nroDoc,
                            "CondicionIVAReceptorId" => $tipoCpbte == 1 ? 1 : 5, // 5 = Consumidor Final | 1 = Responsable Inscripto | 4 = Sujeto Exento
                            "CbteDesde" => (int)$proximo,
                            "CbteHasta" => (int)$proximo,
                            "CbteFch" => $fechaFormateada,
                            "FchServDesde" => $fechaFormateada,
                            "FchServHasta" => $fechaFormateada,
                            "FchVtoPago" => $fechaFormateada,
                            "ImpTotal" => (float)$importeTotal,
                            "ImpTotConc" => 0,
                            "ImpNeto" => (float)$importeNeto,
                            "ImpOpEx" => 0,
                            "ImpIVA" => (float)$importeIVA,
                            "ImpTrib" => 0,
                            "MonId" => 'PES',
                            "MonCotiz" => 1
                        ]
                    ]
                ]
            ];
            
            // Incluir objeto Iva solo si el importe de IVA es mayor a 0
            if ($importeIVA > 0) {
                $datos['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Iva'] = [
                    "AlicIva" => [
                        "Id" => 5,
                        "BaseImp" => (float)$importeNeto,
                        "Importe" => (float)$importeIVA
                    ]
                ];
            }

            self::logInfo("Factura armada - Comprobante: $proximo, Importe: $importe");
        } else {
            if (!$nroCpbteCae) {
                throw new \Exception("Número de comprobante asociado requerido para nota de crédito");
            }    

            $datos = [
                'Auth' => [
                'Token' => $token,
                'Sign' => $sign,
                'Cuit' => $cuit
            ],
                'FeCAEReq' => [
                    'FeCabReq' => [
                        'CantReg' => 1,
                            'PtoVta' => (int)$punto,
                            'CbteTipo' => (int)$tipoCpbte
                    ],
                    'FeDetReq' => [
                        'FECAEDetRequest' => [
                            "Concepto" => 2,
                            "DocTipo" => 96,
                            "DocNro" => (int)$nroDoc,
                            "CondicionIVAReceptorId" => 5,
                            "CbteDesde" => (int)$proximo,
                            "CbteHasta" => (int)$proximo,
                            "CbteFch" => $fechaFormateada,
                            "FchServDesde" => $fechaFormateada,
                            "FchServHasta" => $fechaFormateada,
                            "FchVtoPago" => $fechaFormateada,
                            "ImpTotal" => (float)$importe,
                            "ImpTotConc" => 0,
                            "ImpNeto" => (float)$importe,
                            "ImpOpEx" => 0,
                            "ImpIVA" => 0,
                            "ImpTrib" => 0,
                            "MonId" => 'PES',
                            "MonCotiz" => 1,
                            "CbtesAsoc" => [
                                "CbteAsoc" => [
                                    "Tipo" => 11,
                                        "PtoVta" => (int)$punto,
                                        "Nro" => (int)$nroCpbteCae
                                ],
                            ],
                        ]
                    ]
                ]
            ];
            self::logInfo("Nota de crédito armada - Comprobante: $proximo, Asociado: $nroCpbteCae");
        } 

            // 8. Enviar factura con lógica de reintentos (incluye errores de respuesta AFIP)
            $maxReintentos = 1;
            $response = null;
            $ultimoError = null;
            $esErrorTemporal = false;
            
            for ($intento = 1; $intento <= $maxReintentos; $intento++) {
                try {
                    self::logInfo("Enviando solicitud CAE - Intento $intento/$maxReintentos");
                    $response = $client->FECAESolicitar($datos);
                    
                    // 9. Verificar errores en la respuesta de AFIP
                    $resultado = $response->FECAESolicitarResult;
                    
                    if (isset($resultado->Errors) && $resultado->Errors) {
                        $errores = is_array($resultado->Errors->Err) ? $resultado->Errors->Err : [$resultado->Errors->Err];
                        $mensajesError = [];
                        $codigosError = [];
                        
                        foreach ($errores as $error) {
                            $mensajesError[] = "Código {$error->Code}: {$error->Msg}";
                            $codigosError[] = $error->Code;
                        }
                        
                        // Verificar si es error temporal (500, EsPtoVtaMono)
                        $esErrorTemporal = false;
                        foreach ($codigosError as $codigo) {
                            if ($codigo == 500 || strpos($error->Msg ?? '', 'EsPtoVtaMono') !== false) {
                                $esErrorTemporal = true;
                                break;
                            }
                        }
                        
                        $mensajeCompleto = "Errores AFIP: " . implode(", ", $mensajesError);
                        $ultimoError = new \Exception($mensajeCompleto);
                        
                        self::logError("Error AFIP en intento $intento: $mensajeCompleto");
                        
                        if ($esErrorTemporal && $intento < $maxReintentos) {
                            $segundos = $intento * 5; // 5, 10, 15 segundos
                            self::logInfo("Error temporal de AFIP (código 500/EsPtoVtaMono), esperando $segundos segundos antes del siguiente intento");
                            sleep($segundos);
                            continue; // Reintentar
                        } else {
                            // Error no temporal o se agotaron los reintentos
                            if ($esErrorTemporal) {
                                // Personalizar mensaje para errores temporales que persisten
                                $mensajePersonalizado = "🚫 Error de servidores AFIP después de $intento intentos\n\n";
                                $mensajePersonalizado .= "El servicio de AFIP está experimentando problemas técnicos (código 500 - EsPtoVtaMono).\n\n";
                                $mensajePersonalizado .= "📋 Recomendaciones:\n";
                                $mensajePersonalizado .= "• Espere 15-20 minutos y vuelva a intentar\n";
                                $mensajePersonalizado .= "• Verifique el estado de servicios de AFIP en su sitio web oficial\n";
                                $mensajePersonalizado .= "• Este error es temporal y se resolverá cuando AFIP corrija sus servidores\n";
                                $mensajePersonalizado .= "• La factura NO se generó, puede reintentarla más tarde sin problemas";
                                
                                throw new \Exception($mensajePersonalizado);
                            } else {
                                throw $ultimoError;
                            }
                        }
                    }
                    
                    // Si llegamos aquí, no hay errores en la respuesta
                    break;
                    
                } catch (\SoapFault $e) {
                    $ultimoError = $e;
                    self::logError("Error SOAP en intento $intento: " . $e->getMessage());
                    
                    // Si es error SOAP 500, esperamos antes de reintentar
                    if (strpos($e->getMessage(), '500') !== false || strpos($e->getMessage(), 'EsPtoVtaMono') !== false) {
                        if ($intento < $maxReintentos) {
                            $segundos = $intento * 5; // 5, 10, 15 segundos
                            self::logInfo("Error temporal SOAP de AFIP, esperando $segundos segundos antes del siguiente intento");
                            sleep($segundos);
                        }
                    } else {
                        // Error SOAP no temporal, no reintentamos
                        break;
                    }
                }
            }
            
            if (!$response) {
                if ($esErrorTemporal) {
                    $mensajeFinal = "🚫 Servicio AFIP no disponible después de $maxReintentos intentos\n\n";
                    $mensajeFinal .= "Los servidores de AFIP están experimentando problemas técnicos.\n\n";
                    $mensajeFinal .= "📋 Recomendaciones:\n";
                    $mensajeFinal .= "• Espere 15-20 minutos y vuelva a intentar\n";
                    $mensajeFinal .= "• Verifique el estado de servicios de AFIP\n";
                    $mensajeFinal .= "• La factura NO se generó, puede reintentarla más tarde sin problemas";
                } else {
                    $mensajeFinal = "Error después de $maxReintentos intentos";
                    if ($ultimoError) {
                        $mensajeFinal .= ": " . $ultimoError->getMessage();
                    }
                }
                throw new \Exception($mensajeFinal);
            }

            // 10. Procesar respuesta exitosa
            self::logInfo("=== INICIANDO PROCESAMIENTO DE RESPUESTA ===");
            
            // Log completo de la respuesta para depuración
            try {
                if (isset($client)) {
                    $responseXml = $client->__getLastResponse();
                    self::logInfo("Response XML completo: " . $responseXml);
                    self::logError("Response XML completo (ERROR LOG): " . $responseXml); // También en error log para asegurar visibilidad
                } else {
                    self::logError("Cliente SOAP no está disponible para obtener response XML");
                }
            } catch (\Exception $logEx) {
                self::logError("Error al obtener response XML: " . $logEx->getMessage());
            }
            
            // Log del objeto response directamente
            self::logInfo("Tipo de response: " . gettype($response));
            self::logInfo("Response completo (print_r): " . print_r($response, true));
            self::logError("Response completo (ERROR LOG): " . print_r($response, true));
            
            // Verificar que la respuesta tenga la estructura esperada
            if (!isset($response->FECAESolicitarResult)) {
                $responseStr = print_r($response, true);
                self::logError("Estructura de respuesta inesperada. Response completo: " . $responseStr);
                $mensaje = "La respuesta de AFIP no tiene la estructura esperada. Response: " . substr($responseStr, 0, 500);
                throw new \Exception($mensaje);
            }
            
            $resultado = $response->FECAESolicitarResult;
            
            // Log estructura del resultado
            $resultadoStr = print_r($resultado, true);
            self::logInfo("Estructura de FECAESolicitarResult: " . $resultadoStr);
            self::logError("Estructura de FECAESolicitarResult (ERROR LOG): " . $resultadoStr);
            
            // Verificar si hay errores en el resultado antes de procesar
            if (isset($resultado->Errors) && $resultado->Errors) {
                $errores = is_array($resultado->Errors->Err) ? $resultado->Errors->Err : [$resultado->Errors->Err];
                $mensajesError = [];
                foreach ($errores as $error) {
                    $mensajesError[] = "Código {$error->Code}: {$error->Msg}";
                }
                $mensajeCompleto = "Errores en respuesta AFIP: " . implode(", ", $mensajesError);
                self::logError($mensajeCompleto);
                throw new \Exception($mensajeCompleto);
            }
            
            // Verificar que exista FeDetResp
            if (!isset($resultado->FeDetResp)) {
                $resultadoStr = print_r($resultado, true);
                self::logError("No se encontró FeDetResp en la respuesta. Resultado completo: " . $resultadoStr);
                $mensaje = "La respuesta de AFIP no contiene FeDetResp. Estructura recibida: " . substr($resultadoStr, 0, 500);
                throw new \Exception($mensaje);
            }
            
            // Verificar que FeDetResp sea un array o un objeto
            $feDetResp = is_array($resultado->FeDetResp) ? $resultado->FeDetResp[0] : $resultado->FeDetResp;
            
            $feDetRespStr = print_r($feDetResp, true);
            self::logInfo("FeDetResp: " . $feDetRespStr);
            self::logError("FeDetResp (ERROR LOG): " . $feDetRespStr);
            
            if (!isset($feDetResp->FECAEDetResponse)) {
                self::logError("No se encontró FECAEDetResponse. FeDetResp completo: " . $feDetRespStr);
                $mensaje = "La respuesta de AFIP no contiene FECAEDetResponse. FeDetResp: " . substr($feDetRespStr, 0, 500);
                throw new \Exception($mensaje);
            }
            
            // FECAEDetResponse puede ser un array o un objeto
            $fecaedetResponse = $feDetResp->FECAEDetResponse;
            
            // Si es un array, tomar el primer elemento
            if (is_array($fecaedetResponse)) {
                self::logInfo("FECAEDetResponse es un array con " . count($fecaedetResponse) . " elemento(s)");
                if (empty($fecaedetResponse)) {
                    throw new \Exception("FECAEDetResponse es un array vacío");
                }
                $detResponse = $fecaedetResponse[0];
            } else {
                // Si es un objeto, usarlo directamente
                self::logInfo("FECAEDetResponse es un objeto");
                $detResponse = $fecaedetResponse;
            }
            
            // Log estructura del detalle de respuesta
            $detResponseStr = print_r($detResponse, true);
            self::logInfo("Estructura de FECAEDetResponse (procesado): " . $detResponseStr);
            self::logError("Estructura de FECAEDetResponse (ERROR LOG): " . $detResponseStr);
            
            $cae = $detResponse->CAE ?? null;
            $fchVto = $detResponse->CAEFchVto ?? null;
            $observaciones = $detResponse->Observaciones->Obs ?? null;
            $resultadoOperacion = $detResponse->Resultado ?? null;

            // Log información de la respuesta para depuración
            self::logInfo("Resultado de operación: " . ($resultadoOperacion ?? 'NULL'));
            self::logInfo("CAE recibido: " . ($cae ?? 'NULL'));
            self::logInfo("Fecha vencimiento: " . ($fchVto ?? 'NULL'));
            self::logError("RESUMEN - Resultado: " . ($resultadoOperacion ?? 'NULL') . ", CAE: " . ($cae ?? 'NULL'));
            
            if ($observaciones) {
                $obsArray = is_array($observaciones) ? $observaciones : [$observaciones];
                foreach ($obsArray as $obs) {
                    self::logInfo("Observación AFIP - Código {$obs->Code}: {$obs->Msg}");
                    self::logError("Observación AFIP - Código {$obs->Code}: {$obs->Msg}");
                }
            } else {
                self::logInfo("No se encontraron observaciones en la respuesta");
                self::logError("No se encontraron observaciones en la respuesta");
            }

            self::logInfo("=== EVALUANDO CONDICIÓN: cae=" . ($cae ? 'SI' : 'NO') . ", resultadoOperacion=" . ($resultadoOperacion ?? 'NULL') . " ===");
            
            if ($cae && $resultadoOperacion === 'A') {
                // Validar que la fecha de vencimiento no sea null antes de formatear
                if (empty($fchVto) || $fchVto === null) {
                    self::logError("Error: La fecha de vencimiento (CAEFchVto) es NULL o vacía");
                    throw new \Exception("Error al procesar la respuesta de AFIP: La fecha de vencimiento del CAE no fue recibida. CAE: $cae");
                }
                
                self::logInfo("CAE obtenido exitosamente: $cae, Vencimiento: $fchVto");
                
                if ($factura == 1) {     
                    $caeSalida = $cae;
                    
                    // Validar que la conversión de fecha sea válida
                    $vtoSalida = date("Y-m-d", strtotime($fchVto));
                    if ($vtoSalida === false || $vtoSalida === '1970-01-01') {
                        self::logError("Error: La fecha de vencimiento no pudo ser convertida. Valor recibido: $fchVto");
                        throw new \Exception("Error al procesar la fecha de vencimiento del CAE. Fecha recibida: $fchVto");
                    }

                    $codigo = self::generarCodigoBarra($cuit.substr('0'.$tipoCpbte, -2).substr('0000'.$punto, -4).$caeSalida.$fchVto);
                    
                    $db->createCommand("update movimiento set cae = :cae, fechavto = :fecha, nrocomprobantecae = :comp,
                    puntoventa = :punto, codigobarra = :codigo where id = :idOp")
                    ->bindValue(':idOp', $idOp)
                    ->bindValue(':cae', $caeSalida)
                    ->bindValue(':fecha', $vtoSalida)
                    ->bindValue(':comp', $proximo)
                    ->bindValue(':punto', $punto)
                    ->bindValue(':codigo', $codigo)
                    ->execute();

                    $db->createCommand("call actualizarNumeroReciboCae(:idOp);")
                    ->bindValue(':idOp', $idOp)
                    ->execute();

                    self::logInfo("Factura actualizada en BD - IdOp: $idOp, CAE: $caeSalida");
                    return ['cae' => $caeSalida, 'vto' => $vtoSalida];
                }

                if ($factura == 0) {         
                    $db->createCommand("update movimiento set NotaCredito = :comp where id = :idOp")
                    ->bindValue(':idOp', $idOp)
                    ->bindValue(':comp', $proximo)
                    ->execute();

                    self::logInfo("Nota de crédito actualizada en BD - IdOp: $idOp, Comprobante: $proximo");
                    return 1;
                }   
            } else {
                self::logError("=== ENTRANDO AL BLOQUE ELSE - FACTURA NO APROBADA ===");
                
                // Construir mensaje de error más detallado con toda la información disponible
                $mensajeError = "Error al generar factura.\n\n";
                
                // Agregar información de la estructura completa
                $mensajeError .= "INFORMACIÓN DE LA RESPUESTA:\n";
                $mensajeError .= "Resultado: " . ($resultadoOperacion ?? 'NULL (no disponible)') . "\n";
                $mensajeError .= "CAE: " . ($cae ?? 'NULL (no recibido)') . "\n";
                $mensajeError .= "Fecha Vencimiento: " . ($fchVto ?? 'NULL') . "\n\n";
                
                if ($resultadoOperacion) {
                    $estados = [
                        'A' => 'Aprobado',
                        'R' => 'Rechazado',
                        'P' => 'Parcialmente aprobado',
                        'O' => 'Observado'
                    ];
                    $estadoTexto = $estados[$resultadoOperacion] ?? $resultadoOperacion;
                    $mensajeError .= "Estado de la operación: $estadoTexto ($resultadoOperacion)\n";
                } else {
                    $mensajeError .= "Estado de la operación: No disponible (NULL)\n";
                    $mensajeError .= "Esto indica que AFIP no devolvió un resultado válido.\n";
                }
                
                if (!$cae) {
                    $mensajeError .= "CAE no recibido.\n";
                }
                
                if ($observaciones) {
                    $obsArray = is_array($observaciones) ? $observaciones : [$observaciones];
                    $mensajes = [];
                    foreach ($obsArray as $obs) {
                        $mensajes[] = "Código {$obs->Code}: {$obs->Msg}";
                    }
                    $mensajeError .= "\nObservaciones de AFIP:\n" . implode("\n", $mensajes);
                } else {
                    $mensajeError .= "\nNo se recibieron observaciones adicionales de AFIP.\n";
                }
                
                // Agregar estructura completa al mensaje de error
                $mensajeError .= "\n\nESTRUCTURA COMPLETA DE LA RESPUESTA:\n";
                $mensajeError .= "FECAEDetResponse: " . substr($detResponseStr ?? print_r($detResponse, true), 0, 1000);
                
                // Log del request y response para depuración
                try {
                    if (isset($client)) {
                        $requestXml = $client->__getLastRequest();
                        $responseXml = $client->__getLastResponse();
                        self::logError("Request SOAP completo: " . $requestXml);
                        self::logError("Response SOAP completo: " . $responseXml);
                        $mensajeError .= "\n\nPara más detalles, revise los logs en: runtime/logs/afip_cae.log";
                    }
                } catch (\Exception $logEx) {
                    self::logError("Error al obtener request/response: " . $logEx->getMessage());
                }
                
                self::logError("Mensaje de error completo: " . $mensajeError);
                
                throw new \Exception($mensajeError);
            }
            
        } catch (\Exception $e) {
            self::logError("Error en getCaeDirecto: " . $e->getMessage());
            
            // Si es un error de AFIP temporal, retornar estructura amigable
            if (strpos($e->getMessage(), '🚫 Error de servidores AFIP') !== false || 
                strpos($e->getMessage(), 'código 500') !== false || 
                strpos($e->getMessage(), 'EsPtoVtaMono') !== false) {
                
                return [
                    'error' => true,
                    'tipo' => 'afip_temporal',
                    'mensaje' => $e->getMessage(),
                    'mostrar_amigable' => true
                ];
            }
            
            // Para otros errores, lanzar excepción normal
            throw $e;
        }
    }

    public static function getTaDirecto($cuit, $crt, $key, $esProduccion = false) {
        // Determinar la URL de WSAA según el ambiente
        $wsaaUrl = $esProduccion 
            ? 'https://wsaa.afip.gov.ar/ws/services/LoginCms?wsdl'  // Producción
            : 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms?wsdl'; // Homologación
            
        $wsaa = new AfipWsaaService($crt, $key, $cuit, 'wsfe', $wsaaUrl);
        $ta = $wsaa->obtenerTA();

        return $ta;
    }

    private static function getWsdlPath($service) {
        $wsdlDir = __DIR__ . '/../wsdl/afip/';
        $localFile = $wsdlDir . $service . '.wsdl';
        
        // Verificar si el directorio existe y es accesible
        if (!is_dir($wsdlDir)) {
            self::logError("El directorio WSDL no existe: $wsdlDir");
        } else {
            // Verificar permisos del directorio
            if (!is_readable($wsdlDir)) {
                self::logError("El directorio WSDL no es legible (permisos): $wsdlDir");
            } else {
                self::logInfo("Directorio WSDL accesible: $wsdlDir");
            }
        }
        
        // Si existe el archivo local, verificar que sea legible
        if (file_exists($localFile)) {
            if (is_readable($localFile)) {
                // Intentar leer el archivo para verificar permisos completos
                $content = @file_get_contents($localFile);
                if ($content !== false && !empty($content)) {
                    self::logInfo("WSDL local encontrado y legible: $localFile (tamaño: " . strlen($content) . " bytes)");
                    return $localFile;
                } else {
                    self::logError("WSDL local existe pero no se puede leer o está vacío: $localFile");
                    // Verificar permisos específicos
                    $perms = substr(sprintf('%o', fileperms($localFile)), -4);
                    self::logError("Permisos del archivo: $perms");
                }
            } else {
                self::logError("WSDL local existe pero no es legible (permisos): $localFile");
                $perms = substr(sprintf('%o', fileperms($localFile)), -4);
                self::logError("Permisos del archivo: $perms");
            }
        } else {
            self::logInfo("WSDL local no encontrado: $localFile");
        }
        
        // Si no existe localmente o no es legible, usar la URL remota
        $urls = [
            'wsaa' => 'https://wsaa.afip.gov.ar/ws/services/LoginCms?wsdl',
            'wsfev1' => 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL',
            'wsfev1-homo' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL'
        ];
        
        $remoteUrl = $urls[$service] ?? $urls['wsfev1'];
        self::logInfo("Usando URL remota como fallback: $remoteUrl");
        
        return $remoteUrl;
    }

    private static function logInfo($message) {
        $logFile = __DIR__ . '/../runtime/logs/afip_cae.log';
        $logDir = dirname($logFile);
        
        // Crear directorio si no existe
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Asegurar zona horaria de Buenos Aires para logs
        $oldTz = date_default_timezone_get();
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $timestamp = date('Y-m-d H:i:s T');
        date_default_timezone_set($oldTz);
        
        $logMessage = "[INFO] $timestamp - $message" . PHP_EOL;
        
        // Escribir al archivo específico de AFIP
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        
        // También al log del sistema para backup
        error_log("[AFIP CAE INFO] $timestamp - $message");
    }

    private static function logError($message) {
        $logFile = __DIR__ . '/../runtime/logs/afip_cae.log';
        $logDir = dirname($logFile);
        
        // Crear directorio si no existe
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Asegurar zona horaria de Buenos Aires para logs
        $oldTz = date_default_timezone_get();
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $timestamp = date('Y-m-d H:i:s T');
        date_default_timezone_set($oldTz);
        
        $logMessage = "[ERROR] $timestamp - $message" . PHP_EOL;
        
        // Escribir al archivo específico de AFIP
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        
        // También al log del sistema para backup
        error_log("[AFIP CAE ERROR] $timestamp - $message");
    }

    public static function generarCodigoBarra($cadena) {
        $largo = strlen($cadena);
        $sumaImpar = 0;
        $sumaPar = 0;
    
        // Convertir la cadena en un array de números
        $c = str_split($cadena);
    
        // Sumar posiciones pares e impares
        for ($i = 0; $i < $largo; $i++) {
            $num = (int) $c[$i]; // Convertir caracter a entero
            if (($i + 1) % 2 == 0) { // Índice en base 1
                $sumaPar += $num;
            } else {
                $sumaImpar += $num;
            }
        }
    
        // Calcular el dígito verificador
        $total = ($sumaImpar * 3) + $sumaPar;
        $digito = substr($total, -1); // Último dígito
    
        // Si el último dígito es 0, se mantiene; si no, se calcula 10 - digito
        $digitoVerificador = ($digito == '0') ? 0 : 10 - (int) $digito;
    
        // Retornar el código con el dígito verificador agregado
        return $cadena . $digitoVerificador;
    }
}
