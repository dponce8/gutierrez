<?php

namespace app\controllers;

use Yii;
use TCPDF;
use app\controllers\CajaController;

class ViajeController extends \yii\web\Controller
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

        $personas = $db->createCommand("select id, concat(apellido,' ',nombre) as persona 
        from persona where id_tipo_persona = 1 order by persona;")
        ->queryAll();

        return $this->render('index', ['personas' => $personas]);
    }

    public function actionViajeLista($idPersona, $desde = "", $hasta = "")  
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;

        if ($request->get('eliminar') == 1) {
            $idViaje = $request->get('idViaje');
            $db->createCommand("delete from viaje where id = :id")->bindValue(':id', $idViaje)->execute();
            $db->createCommand("delete from persona_movimiento where id_viaje = :id")->bindValue(':id', $idViaje)->execute();
            $db->createCommand("delete from movimiento where id_viaje = :id")->bindValue(':id', $idViaje)->execute();
        }

        if ($request->get('guardar') == 1) {
            $transaction = \Yii::$app->db->beginTransaction();

            $cliente = $request->get('cliente');
            $fecha_salida = $request->get('fecha_salida');
            $hora_salida = $request->get('hora_salida');
            $fecha_regreso = $request->get('fecha_regreso');
            $hora_regreso = $request->get('hora_regreso');
            $origen = $request->get('origen');
            $destino = $request->get('destino');
            $direccion_origen = $request->get('direccion_origen');
            $direccion_destino = $request->get('direccion_destino');
            $coord_origen = $request->get('coord_origen');
            $coord_destino = $request->get('coord_destino');
            $chofer_1 = $request->get('chofer_1');
            $chofer_2 = $request->get('chofer_2');
            $coche = $request->get('coche');
            $total = floatval($request->get('total'));
            $anticipo = floatval($request->get('anticipo'));
            $medio = (int)$request->get('medio');
            $obs = $request->get('obs');
            $id_usuario = Yii::$app->user->identity->id;
            $pasajeros = $request->get('pasajeros');
            $empresa = $request->get('empresa');

            // VALIDACIONES DE DISPONIBILIDAD
            $errores = [];
            
            // Verificar disponibilidad del vehículo
            if (!empty($coche) && $coche > 0) {
                $vehiculoOcupado = $db->createCommand("
                    SELECT COUNT(*) as ocupado 
                    FROM viaje 
                    WHERE id_vehiculo = :vehiculo 
                    AND (
                        -- El nuevo viaje inicia antes de que termine un viaje existente
                        (CONCAT(:fecha_salida, ' ', :hora_salida) < CONCAT(fecha_regreso, ' ', hora_regreso))
                        AND
                        -- El nuevo viaje termina después de que inicie un viaje existente
                        (CONCAT(:fecha_regreso, ' ', :hora_regreso) > CONCAT(fecha_salida, ' ', hora_salida))
                    )
                ")
                ->bindValue(':vehiculo', $coche)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->queryScalar();
                
                if ($vehiculoOcupado > 0) {
                    $errores[] = "El vehículo seleccionado ya está asignado a otro viaje en las fechas y horas indicadas.";
                }
            }
            
            // Verificar disponibilidad del chofer 1
            if (!empty($chofer_1) && $chofer_1 > 0) {
                $chofer1Ocupado = $db->createCommand("
                    SELECT COUNT(*) as ocupado 
                    FROM viaje 
                    WHERE (id_chofer_1 = :chofer OR id_chofer_2 = :chofer)
                    AND (
                        -- El nuevo viaje inicia antes de que termine un viaje existente
                        (CONCAT(:fecha_salida, ' ', :hora_salida) < CONCAT(fecha_regreso, ' ', hora_regreso))
                        AND
                        -- El nuevo viaje termina después de que inicie un viaje existente
                        (CONCAT(:fecha_regreso, ' ', :hora_regreso) > CONCAT(fecha_salida, ' ', hora_salida))
                    )
                ")
                ->bindValue(':chofer', $chofer_1)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->queryScalar();
                
                if ($chofer1Ocupado > 0) {
                    $errores[] = "El primer chofer seleccionado ya está asignado a otro viaje en las fechas y horas indicadas.";
                }
            }
            
            // Verificar disponibilidad del chofer 2
            if (!empty($chofer_2) && $chofer_2 > 0) {
                $chofer2Ocupado = $db->createCommand("
                    SELECT COUNT(*) as ocupado 
                    FROM viaje 
                    WHERE (id_chofer_1 = :chofer OR id_chofer_2 = :chofer)
                    AND (
                        -- El nuevo viaje inicia antes de que termine un viaje existente
                        (CONCAT(:fecha_salida, ' ', :hora_salida) < CONCAT(fecha_regreso, ' ', hora_regreso))
                        AND
                        -- El nuevo viaje termina después de que inicie un viaje existente
                        (CONCAT(:fecha_regreso, ' ', :hora_regreso) > CONCAT(fecha_salida, ' ', hora_salida))
                    )
                ")
                ->bindValue(':chofer', $chofer_2)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->queryScalar();
                
                if ($chofer2Ocupado > 0) {
                    $errores[] = "El segundo chofer seleccionado ya está asignado a otro viaje en las fechas y horas indicadas.";
                }
            }
            
            // Si hay errores, no continuar con la inserción
            if (!empty($errores)) {
                $transaction->rollBack();
                return json_encode([
                    'success' => false,
                    'errores' => $errores,
                    'mensaje' => 'No se puede guardar el viaje debido a conflictos de asignación.'
                ]);
            }

            $db->createCommand("insert into viaje (id_cliente,fecha, fecha_salida, hora_salida, fecha_regreso, hora_regreso, origen, destino, 
            direccion_origen, direccion_destino, coord_origen, coord_destino, id_chofer_1, id_chofer_2, total, anticipo, obs, id_usuario,
            id_vehiculo, pasajeros, id_empresa) 
            values (:cliente,curdate(), :fecha_salida, :hora_salida, :fecha_regreso, :hora_regreso, :origen, :destino, :direccion_origen, 
            :direccion_destino, :coord_origen, :coord_destino, :chofer_1, :chofer_2, :total, :anticipo, :obs, :id_usuario, :id_vehiculo, 
            :pasajeros, :id_empresa)")
            ->bindValue(':cliente', $cliente)
            ->bindValue(':fecha_salida', $fecha_salida)
            ->bindValue(':hora_salida', $hora_salida)
            ->bindValue(':fecha_regreso', $fecha_regreso)
            ->bindValue(':hora_regreso', $hora_regreso)
            ->bindValue(':origen', $origen)
            ->bindValue(':destino', $destino)
            ->bindValue(':direccion_origen', $direccion_origen)
            ->bindValue(':direccion_destino', $direccion_destino)
            ->bindValue(':coord_origen', $coord_origen)
            ->bindValue(':coord_destino', $coord_destino)
            ->bindValue(':chofer_1', $chofer_1)
            ->bindValue(':chofer_2', $chofer_2)
            ->bindValue(':total', $total)
            ->bindValue(':anticipo', $anticipo)
            ->bindValue(':obs', $obs)
            ->bindValue(':id_usuario', $id_usuario)
            ->bindValue(':id_vehiculo', $coche)
            ->bindValue(':pasajeros', $pasajeros)
            ->bindValue(':id_empresa', $empresa)
            ->execute();

            $idViaje = $db->getLastInsertID();

            $db->createCommand("insert into persona_movimiento (id_persona, id_movimiento_tipo, importe, fecha, hora, id_usuario, obs, id_viaje, id_empresa) 
            values(:id_persona, 1, :importe, curdate(), curtime(), :id_usuario, :obs, :idViaje, :id_empresa)")
            ->bindValue(':id_persona', (int)$cliente)
            ->bindValue(':importe', $total)
            ->bindValue(':id_usuario', Yii::$app->user->identity->id)
            ->bindValue(':obs', 'Viaje Especial N° ' . $idViaje)
            ->bindValue(':idViaje', $idViaje)
            ->bindValue(':id_empresa', $empresa)
            ->execute();

            if ($anticipo > 0) {
                $db->createCommand("insert into movimiento (fecha, hora, id_concepto, id_persona, id_usuario, importe, id_viaje, obs, id_empresa, nro_comprobante) 
                values(curdate(), curtime(), 1, :id_persona, :id_usuario, :importe, :id_viaje, :obs, :id_empresa, :nro_comprobante)")
                ->bindValue(':id_persona', $cliente)
                ->bindValue(':id_usuario', Yii::$app->user->identity->id)
                ->bindValue(':importe', $anticipo)
                ->bindValue(':id_viaje', $idViaje)
                ->bindValue(':obs', 'Anticipo Viaje Especial N° ' . $idViaje)
                ->bindValue(':id_empresa', $empresa)
                ->bindValue(':nro_comprobante', CajaController::getNroComprobante(1))
                ->execute();

                $idMov = $db->getLastInsertID();

                $db->createCommand("insert into movimiento_medio (id_movimiento, id_medio, importe,id_cheque, id_cuenta, id_tarjeta)
                values(:idMov, :id_medio, :importe,0, 0, 0)")
                ->bindValue(':idMov', (int)$idMov)
                ->bindValue(':id_medio', $medio)
                ->bindValue(':importe', $anticipo)
                ->execute();

                $db->createCommand("insert into persona_movimiento (id_persona, id_movimiento_tipo, importe, fecha, hora, id_usuario, obs, id_viaje, id_movimiento_caja, id_empresa) 
                values(:id_persona, 2, :importe, curdate(), curtime(), :id_usuario, :obs, :idViaje, :idMov, :id_empresa)")
                ->bindValue(':id_persona', (int)$cliente)
                ->bindValue(':importe', $anticipo)
                ->bindValue(':id_usuario', Yii::$app->user->identity->id)
                ->bindValue(':obs', 'Anticipo Viaje Especial N° ' . $idViaje)
                ->bindValue(':idViaje', $idViaje)
                ->bindValue(':idMov', $idMov)
                ->bindValue(':id_empresa', $empresa)
                ->execute();
            }

            $transaction->commit();
            
            // Respuesta exitosa
            return json_encode([
                'success' => true,
                'mensaje' => 'Viaje guardado exitosamente.',
                'viaje_id' => $idViaje
            ]);
        }

        if ($request->get('modificar') == 1) {
            $transaction = \Yii::$app->db->beginTransaction();

            $idViaje = $request->get('idViaje');
            $cliente = $request->get('cliente');
            $fecha_salida = $request->get('fecha_salida');
            $hora_salida = $request->get('hora_salida');
            $fecha_regreso = $request->get('fecha_regreso');
            $hora_regreso = $request->get('hora_regreso');
            $obs = $request->get('obs');
            $chofer_1 = $request->get('chofer_1');
            $chofer_2 = $request->get('chofer_2');
            $coche = $request->get('coche');

            // VALIDACIONES DE DISPONIBILIDAD
            $errores = [];
            
            // Verificar disponibilidad del vehículo
            if (!empty($coche) && $coche > 0) {
                $vehiculoOcupado = $db->createCommand("
                    SELECT COUNT(*) as ocupado 
                    FROM viaje 
                    WHERE id_vehiculo = :vehiculo 
                    AND (
                        -- El nuevo viaje inicia antes de que termine un viaje existente
                        (CONCAT(:fecha_salida, ' ', :hora_salida) < CONCAT(fecha_regreso, ' ', hora_regreso))
                        AND
                        -- El nuevo viaje termina después de que inicie un viaje existente
                        (CONCAT(:fecha_regreso, ' ', :hora_regreso) > CONCAT(fecha_salida, ' ', hora_salida))
                    )
                ")
                ->bindValue(':vehiculo', $coche)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->queryScalar();
                
                if ($vehiculoOcupado > 0) {
                    $errores[] = "El vehículo seleccionado ya está asignado a otro viaje en las fechas y horas indicadas.";
                }
            }
            
            // Verificar disponibilidad del chofer 1
            if (!empty($chofer_1) && $chofer_1 > 0) {
                $chofer1Ocupado = $db->createCommand("
                    SELECT COUNT(*) as ocupado 
                    FROM viaje 
                    WHERE (id_chofer_1 = :chofer OR id_chofer_2 = :chofer)
                    AND (
                        -- El nuevo viaje inicia antes de que termine un viaje existente
                        (CONCAT(:fecha_salida, ' ', :hora_salida) < CONCAT(fecha_regreso, ' ', hora_regreso))
                        AND
                        -- El nuevo viaje termina después de que inicie un viaje existente
                        (CONCAT(:fecha_regreso, ' ', :hora_regreso) > CONCAT(fecha_salida, ' ', hora_salida))
                    )
                ")
                ->bindValue(':chofer', $chofer_1)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->queryScalar();
                
                if ($chofer1Ocupado > 0) {
                    $errores[] = "El primer chofer seleccionado ya está asignado a otro viaje en las fechas y horas indicadas.";
                }
            }
            
            // Verificar disponibilidad del chofer 2
            if (!empty($chofer_2) && $chofer_2 > 0) {
                $chofer2Ocupado = $db->createCommand("
                    SELECT COUNT(*) as ocupado 
                    FROM viaje 
                    WHERE (id_chofer_1 = :chofer OR id_chofer_2 = :chofer)
                    AND (
                        -- El nuevo viaje inicia antes de que termine un viaje existente
                        (CONCAT(:fecha_salida, ' ', :hora_salida) < CONCAT(fecha_regreso, ' ', hora_regreso))
                        AND
                        -- El nuevo viaje termina después de que inicie un viaje existente
                        (CONCAT(:fecha_regreso, ' ', :hora_regreso) > CONCAT(fecha_salida, ' ', hora_salida))
                    )
                ")
                ->bindValue(':chofer', $chofer_2)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->queryScalar();
                
                if ($chofer2Ocupado > 0) {
                    $errores[] = "El segundo chofer seleccionado ya está asignado a otro viaje en las fechas y horas indicadas.";
                }
            }
            
            // Si hay errores, no continuar con la inserción
            if (!empty($errores)) {
                $transaction->rollBack();
                return json_encode([
                    'success' => false,
                    'errores' => $errores,
                    'mensaje' => 'No se puede guardar el viaje debido a conflictos de asignación.'
                ]);
            }

            $db->createCommand("update viaje set fecha_salida = :fecha_salida, hora_salida = :hora_salida, fecha_regreso = :fecha_regreso, hora_regreso = :hora_regreso, 
            id_chofer_1 = :chofer_1, id_chofer_2 = :chofer_2, id_vehiculo = :id_vehiculo, obs = :obs
            where id = :id")
            ->bindValue(':id', $idViaje)
            ->bindValue(':fecha_salida', $fecha_salida)
            ->bindValue(':hora_salida', $hora_salida)
            ->bindValue(':fecha_regreso', $fecha_regreso)
            ->bindValue(':hora_regreso', $hora_regreso)
            ->bindValue(':chofer_1', $chofer_1)
            ->bindValue(':chofer_2', $chofer_2)
            ->bindValue(':id_vehiculo', $coche)
            ->bindValue(':obs', $obs)
            ->execute();

            $transaction->commit();
            
            // Respuesta exitosa
            return json_encode([
                'success' => true,
                'mensaje' => 'Viaje guardado exitosamente.',
                'viaje_id' => $idViaje
            ]);
        }

        $per1 = $idPersona; $per2 = $idPersona;
        if ($idPersona == 0) {
            $per1 = 1;
            $per2 = 9999;
        }

        $listado = $db->createCommand("
        select v.*, concat(p.apellido,' ',p.nombre) cliente,
        concat(e1.apellido,' ',e1.nombre) chofer_1,
        concat(e2.apellido,' ',e2.nombre) chofer_2,
        concat(u.apellido,' ',u.nombre) usuario,
        lo.localidad local_origen, ld.localidad local_destino,
        po.provincia pcia_origen, pd.provincia pcia_destino,
        pao.pais pais_origen, pad.pais pais_destino,
        vh.numero_interno coche,
        se.Empresa empresa
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
        where v.id_cliente between :per1 and :per2 
        and v.fecha between :desde and :hasta;")
        ->bindValue(':per1', $per1)
        ->bindValue(':per2', $per2)
        ->bindValue(':desde', $desde)
        ->bindValue(':hasta', $hasta)
        ->queryAll();

        return $this->renderPartial('viaje-lista', ['listado' => $listado]);
    }

    public function actionViajeCarga()
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $id = (int)$request->get('id');

        $clientes = $db->createCommand("select * from persona where id_tipo_persona = 1 order by apellido,nombre")->queryAll();
        $choferes = $db->createCommand("select idempleado, concat(apellido,' ',nombre) as chofer from empleados where idtipoempleado = 2 order by apellido, nombre")->queryAll();
        $medios = $db->createCommand("select id, medio from medio_pago order by id")->queryAll();
        $coches = $db->createCommand("select id, numero_interno, asientos from vehiculo order by numero_interno")->queryAll();
        $empresas = $db->createCommand("select * from sueldosempresas")->queryAll();
        $localidades = $db->createCommand("select l.localidad, p.provincia, pa.pais, l.idlocalidad
        from localidades l 
        join provincia p on p.id = l.id_provincia
        join pais pa on pa.id = p.id_pais
        order by pa.pais, p.provincia, l.localidad")
        ->queryAll();

        $presupuestos = $db->createCommand("select * from presupuesto where id = :id")
        ->bindValue(':id', $id)
        ->queryOne();

        return $this->renderPartial('viaje-carga', ['clientes' => $clientes, 'choferes' => $choferes, 
        'medios' => $medios, 'localidades' => $localidades, 'coches' => $coches, 'empresas' => $empresas, 'presupuestos' => $presupuestos]);
    }
    public function actionPresupuesto()
    {
        $db = Yii::$app->db;

        $personas = $db->createCommand("select id, concat(apellido,' ',nombre) as persona 
        from persona where id_tipo_persona = 1 order by persona;")
        ->queryAll();

        return $this->render('presupuesto', ['personas' => $personas]);
    }

    public function actionPresupuestoLista($idPersona, $desde = "", $hasta = "")  
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $nuevo = 0;

        if ($request->get('eliminar') == 1) {
            $idPresupuesto = $request->get('idPresupuesto');
            $db->createCommand("delete from presupuesto where id = :id")->bindValue(':id', $idPresupuesto)->execute();
        }

        if ($request->get('aprobar') == 1) {
            $idPresupuesto = $request->get('idPresupuesto');
            $db->createCommand("update presupuesto set id_estado = 1 where id = :id")->bindValue(':id', $idPresupuesto)->execute();
        }

        if ($request->get('guardar') == 1) {
            $transaction = \Yii::$app->db->beginTransaction();

            $cliente = $request->get('cliente');
            $fecha_salida = $request->get('fecha_salida');
            $hora_salida = $request->get('hora_salida');
            $fecha_regreso = $request->get('fecha_regreso');
            $hora_regreso = $request->get('hora_regreso');
            $origen = $request->get('origen');
            $destino = $request->get('destino');
            $direccion_origen = $request->get('direccion_origen');
            $direccion_destino = $request->get('direccion_destino');
            $total = floatval($request->get('total'));
            $obs = $request->get('obs');
            $obs_interna = $request->get('obs_interna');
            $tipo_unidad = $request->get('tipo_unidad');
            $id_usuario = Yii::$app->user->identity->id;
            $pasajeros = $request->get('pasajeros');
            $fecha_vto = $request->get('fecha_vto');
            $id_presupuesto = $request->get('id_presupuesto');
            if ($id_presupuesto == 0) {
                $db->createCommand("insert into presupuesto (id_cliente,fecha, fecha_salida, hora_salida, fecha_regreso, hora_regreso, origen, destino, 
                direccion_origen, direccion_destino, total, obs, obs_interna, id_usuario, pasajeros, fecha_vto, id_tipo_coche) 
                values (:cliente,curdate(), :fecha_salida, :hora_salida, :fecha_regreso, :hora_regreso, :origen, :destino, :direccion_origen, 
                :direccion_destino, :total, :obs,:obs_interna, :id_usuario, :pasajeros, :fecha_vto, :id_tipo_coche)")
                ->bindValue(':cliente', $cliente)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->bindValue(':origen', $origen)
                ->bindValue(':destino', $destino)
                ->bindValue(':direccion_origen', $direccion_origen)
                ->bindValue(':direccion_destino', $direccion_destino)
                ->bindValue(':total', $total)
                ->bindValue(':obs', $obs)
                ->bindValue(':obs_interna', $obs_interna)
                ->bindValue(':id_usuario', $id_usuario)
                ->bindValue(':pasajeros', $pasajeros)
                ->bindValue(':fecha_vto', $fecha_vto)
                ->bindValue(':id_tipo_coche', $tipo_unidad)
                ->execute();

                $nuevo = $db->getLastInsertID();

            } else {
                $id_estado = 0;
                if (Yii::$app->user->identity->id_perfil == 3) {$id_estado = 1;}
                $db->createCommand("update presupuesto set id_cliente = :cliente, fecha = curdate(), 
                fecha_salida = :fecha_salida, hora_salida = :hora_salida, fecha_regreso = :fecha_regreso, 
                hora_regreso = :hora_regreso, origen = :origen, destino = :destino, direccion_origen = :direccion_origen, 
                direccion_destino = :direccion_destino, total = :total, obs = :obs, obs_interna = :obs_interna, id_usuario = :id_usuario, pasajeros = :pasajeros, id_tipo_coche = :id_tipo_coche,
                fecha_vto = :fecha_vto, id_estado = :id_estado
                where id = :id")
                ->bindValue(':cliente', $cliente)
                ->bindValue(':fecha_salida', $fecha_salida)
                ->bindValue(':hora_salida', $hora_salida)
                ->bindValue(':fecha_regreso', $fecha_regreso)
                ->bindValue(':hora_regreso', $hora_regreso)
                ->bindValue(':origen', $origen)
                ->bindValue(':destino', $destino)
                ->bindValue(':direccion_origen', $direccion_origen)
                ->bindValue(':direccion_destino', $direccion_destino)
                ->bindValue(':total', $total)
                ->bindValue(':obs', $obs)
                ->bindValue(':obs_interna', $obs_interna)
                ->bindValue(':id_usuario', $id_usuario)
                ->bindValue(':id_tipo_coche', $tipo_unidad)
                ->bindValue(':pasajeros', $pasajeros)
                ->bindValue(':fecha_vto', $fecha_vto)
                ->bindValue(':id', $id_presupuesto)
                ->bindValue(':id_estado', $id_estado)
                ->execute();
            }
            $transaction->commit();
        }

        $per1 = $idPersona; $per2 = $idPersona;
        if ($idPersona == 0) {
            $per1 = 1;
            $per2 = 9999;
        }

        $listado = $db->createCommand("
        select v.*, concat(p.apellido,' ',p.nombre) cliente,
        concat(u.apellido,' ',u.nombre) usuario,
        lo.localidad local_origen, ld.localidad local_destino,
        po.provincia pcia_origen, pd.provincia pcia_destino,
        pao.pais pais_origen, pad.pais pais_destino,
        ct.tipo tipo_unidad
        from presupuesto v
        join persona p on p.id = v.id_cliente
        left join user u on u.id = v.id_usuario
        left join localidades lo on lo.idlocalidad = v.origen
        left join localidades ld on ld.idlocalidad = v.destino
        left join provincia po on po.id = lo.id_provincia
        left join provincia pd on pd.id = ld.id_provincia
        left join pais pao on pao.id = po.id_pais
        left join pais pad on pad.id = po.id_pais
        left join coche_tipo ct on ct.id = v.id_tipo_coche
        where v.id_cliente between :per1 and :per2 
        and v.fecha between :desde and :hasta;")
        ->bindValue(':per1', $per1)
        ->bindValue(':per2', $per2)
        ->bindValue(':desde', $desde)
        ->bindValue(':hasta', $hasta)
        ->queryAll();

        return $this->renderPartial('presupuesto-lista', ['listado' => $listado, 'nuevo' => $nuevo]);
    }   

    public function actionPresupuestoCarga($id)
    {
        $db = Yii::$app->db;

        $clientes = $db->createCommand("select * from persona where id_tipo_persona = 1 order by apellido,nombre")->queryAll();
        $localidades = $db->createCommand("select l.localidad, p.provincia, pa.pais, l.idlocalidad
        from localidades l 
        join provincia p on p.id = l.id_provincia
        join pais pa on pa.id = p.id_pais
        order by pa.pais, p.provincia, l.localidad")
        ->queryAll();
        $tipoCoche = $db->createCommand("select * from coche_tipo order by tipo")->queryAll();

        $infoPresupuesto = null;
        if ($id != 0) {
            $infoPresupuesto = $db->createCommand("select * from presupuesto where id = :id")->bindValue(':id', $id)->queryOne();
        }

        return $this->renderPartial('presupuesto-carga', ['clientes' => $clientes,  'localidades' => $localidades, 
        'infoPresupuesto' => $infoPresupuesto, 'tipoCoche' => $tipoCoche]);
    }

    /**
     * Devuelve la lista de clientes (personas tipo cliente) en JSON para refrescar selects.
     */
    public function actionClientesList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $db = Yii::$app->db;
        $rows = $db->createCommand("SELECT id, apellido, nombre FROM persona WHERE id_tipo_persona = 1 ORDER BY apellido, nombre")
            ->queryAll();
        return $rows;
    }

    /**
     * Devuelve la lista de localidades (para origen/destino en presupuesto) en JSON.
     */
    public function actionLocalidadesList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $db = Yii::$app->db;
        $rows = $db->createCommand("SELECT l.idlocalidad, l.localidad, p.provincia, pa.pais
            FROM localidades l
            JOIN provincia p ON p.id = l.id_provincia
            JOIN pais pa ON pa.id = p.id_pais
            ORDER BY pa.pais, p.provincia, l.localidad")
            ->queryAll();
        return $rows;
    }

    public function actionPresupuestoImprime($id)
    {        
        $db = Yii::$app->db;

        $datos = $db->createCommand("select v.*, concat(p.apellido,' ',p.nombre) cliente,
        concat(u.apellido,' ',u.nombre) usuario,
        lo.localidad local_origen, ld.localidad local_destino,
        po.provincia pcia_origen, pd.provincia pcia_destino,
        pao.pais pais_origen, pad.pais pais_destino, p.cuit,
        p.domicilio, p.email,p.fijo, p.celular,
        ct.tipo tipo_unidad
        from presupuesto v
        join persona p on p.id = v.id_cliente
        left join user u on u.id = v.id_usuario
        left join localidades lo on lo.idlocalidad = v.origen
        left join localidades ld on ld.idlocalidad = v.destino
        left join provincia po on po.id = lo.id_provincia
        left join provincia pd on pd.id = ld.id_provincia
        left join pais pao on pao.id = po.id_pais
        left join pais pad on pad.id = po.id_pais
        left join coche_tipo ct on ct.id = v.id_tipo_coche
        where v.id = :id")
        ->bindValue(':id', $id)
        ->queryOne();

        require_once('../vendor/tcpdf/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Presupuesto');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10, false);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('P', 'A4');
        $tbl = $this->renderPartial('_presupuesto', ['datos' => $datos]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        ob_end_clean();
        $pdf->Output('presupuesto_imprime.pdf', 'I');
    }    

    public function actionViajePresupuesto()
    {
        $db = Yii::$app->db;

        $personas = $db->createCommand("select id, concat(apellido,' ',nombre) as persona 
        from persona where id_tipo_persona = 1 order by persona;")
        ->queryAll();

        return $this->renderPartial('viaje-presupuesto', ['personas' => $personas]);
    }

    public function actionViajePresupuestoLista($idPersona, $nro)  
    {
        $db = Yii::$app->db;

        if ((int)$nro > 0) {
            $listado = $db->createCommand("
            select v.*, concat(p.apellido,' ',p.nombre) cliente,
            concat(u.apellido,' ',u.nombre) usuario,
            lo.localidad local_origen, ld.localidad local_destino,
            po.provincia pcia_origen, pd.provincia pcia_destino,
            pao.pais pais_origen, pad.pais pais_destino,
            ct.tipo tipo_unidad
            from presupuesto v
            join persona p on p.id = v.id_cliente
            left join user u on u.id = v.id_usuario
            left join localidades lo on lo.idlocalidad = v.origen
            left join localidades ld on ld.idlocalidad = v.destino
            left join provincia po on po.id = lo.id_provincia
            left join provincia pd on pd.id = ld.id_provincia
            left join pais pao on pao.id = po.id_pais
            left join pais pad on pad.id = po.id_pais
            left join coche_tipo ct on ct.id = v.id_tipo_coche
            where v.id = :nro and v.id_estado = 1;")
            ->bindValue(':nro', $nro)
            ->queryAll();
        } else {
            $per1 = $idPersona; $per2 = $idPersona;
            if ($idPersona == 0) {
                $per1 = 1;
                $per2 = 9999;
            }
            $listado = $db->createCommand("
            select v.*, concat(p.apellido,' ',p.nombre) cliente,
            concat(u.apellido,' ',u.nombre) usuario,
            lo.localidad local_origen, ld.localidad local_destino,
            po.provincia pcia_origen, pd.provincia pcia_destino,
            pao.pais pais_origen, pad.pais pais_destino,
            ct.tipo tipo_unidad
            from presupuesto v
            join persona p on p.id = v.id_cliente
            left join user u on u.id = v.id_usuario
            left join localidades lo on lo.idlocalidad = v.origen
            left join localidades ld on ld.idlocalidad = v.destino
            left join provincia po on po.id = lo.id_provincia
            left join provincia pd on pd.id = ld.id_provincia
            left join pais pao on pao.id = po.id_pais
            left join pais pad on pad.id = po.id_pais
            left join coche_tipo ct on ct.id = v.id_tipo_coche
            where v.id_cliente between :per1 and :per2 and v.id_estado = 1")
            ->bindValue(':per1', $per1)
            ->bindValue(':per2', $per2)
            ->queryAll();
        }

        return $this->renderPartial('viaje-presupuesto-lista', ['listado' => $listado]);
    } 

    public function actionVehiculo()
    {
        $db = Yii::$app->db;
        $coches = $db->createCommand("select id, numero_interno, asientos from vehiculo order by numero_interno")->queryAll();

        return $this->render('vehiculo', ['coches' => $coches]);
    }

    public function actionVehiculoLista($idCoche, $mes, $periodo)
    {
        $db = Yii::$app->db;

        // Crear las fechas de inicio y fin del mes consultado
        $fechaInicioMes = $db->createCommand("SELECT DATE('$periodo-$mes-01')")->queryScalar();
        $fechaFinMes = $db->createCommand("SELECT LAST_DAY('$periodo-$mes-01')")->queryScalar();
        
        // Construir la consulta base
        $sql = "
            SELECT v.id, v.fecha_salida, v.fecha_regreso, vh.numero_interno, vh.id as vehiculo_id,
                   DATE_FORMAT(v.fecha_salida, '%d/%m/%Y') as fecha_salida_formatted,
                   DATE_FORMAT(v.fecha_regreso, '%d/%m/%Y') as fecha_regreso_formatted,
                   v.hora_salida, v.hora_regreso,lo.localidad local_origen, ld.localidad local_destino
            FROM viaje v
            JOIN vehiculo vh ON vh.id = v.id_vehiculo
            left join localidades lo on lo.idlocalidad = v.origen
            left join localidades ld on ld.idlocalidad = v.destino
            WHERE 1=1 ";
        
        // Si idCoche es 0, mostrar todos los vehículos, sino filtrar por vehículo específico
        if ($idCoche != 0) {
            $sql .= " AND v.id_vehiculo = :idCoche ";
        }
        
        $sql .= " AND (
                -- El viaje inicia en el mes consultado
                (DATE(v.fecha_salida) >= :fechaInicio AND DATE(v.fecha_salida) <= :fechaFin)
                OR 
                -- El viaje termina en el mes consultado  
                (DATE(v.fecha_regreso) >= :fechaInicio AND DATE(v.fecha_regreso) <= :fechaFin)
                OR
                -- El viaje cruza todo el mes (inicia antes y termina después)
                (DATE(v.fecha_salida) < :fechaInicio AND DATE(v.fecha_regreso) > :fechaFin)
            )
            ORDER BY vh.numero_interno ASC, v.fecha_salida ASC
        ";
        
        $command = $db->createCommand($sql)
            ->bindValue(':fechaInicio', $fechaInicioMes)
            ->bindValue(':fechaFin', $fechaFinMes);
        
        // Solo agregar el bind del idCoche si no es "todos"
        if ($idCoche != 0) {
            $command->bindValue(':idCoche', $idCoche);
        }
        
        $vehiculos = $command->queryAll();
        return $this->renderPartial('vehiculo-lista', ['vehiculos' => $vehiculos]);
    }

    public function actionSaldo()
    {
        $db = Yii::$app->db;

        $personas = $db->createCommand("select id, concat(apellido,' ',nombre) as persona 
        from persona where id_tipo_persona = 1 order by persona;")
        ->queryAll();

        $empresas = $db->createCommand("select idEmpresa, Empresa from sueldosempresas order by Empresa;")
        ->queryAll();

        return $this->render('saldo', ['personas' => $personas, 'empresas' => $empresas]);
    }

    public function actionViajeModifica($id)
    {
        $db = Yii::$app->db;

        $infoViaje = $db->createCommand("select * from viaje where id = :id;")
        ->bindValue(':id', $id)
        ->queryOne();

        $choferes = $db->createCommand("select idempleado, concat(apellido,' ',nombre) as chofer from empleados where idtipoempleado = 2 order by apellido, nombre")->queryAll();
        $coches = $db->createCommand("select id, numero_interno, asientos from vehiculo order by numero_interno")->queryAll();

        return $this->renderPartial('viaje-modifica', ['infoViaje' => $infoViaje, 'choferes' => $choferes,'coches' => $coches]);
    }

    public function actionSaldoLista($idPersona, $empresa)  
    {
        $db = Yii::$app->db;

        $emp1 = $empresa; $emp2 = $empresa;
        if ($empresa == 0) {
            $emp1 = 1;
            $emp2 = 9999;
        }

        $per1 = $idPersona; $per2 = $idPersona;
        if ($idPersona == 0) {
            $per1 = 1;
            $per2 = 9999;
        }

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
        where v.id_cliente between :per1 and :per2 and v.id_empresa between :emp1 and :emp2
        -- and case when pm.importe_pagado is null then 0 else pm.importe_pagado end < v.total")
        ->bindValue(':per1', $per1)
        ->bindValue(':per2', $per2)
        ->bindValue(':emp1', $emp1)
        ->bindValue(':emp2', $emp2)
        ->queryAll();

        return $this->renderPartial('saldo-lista', ['listado' => $listado]);
    }

}
