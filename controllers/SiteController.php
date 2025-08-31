<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use TCPDF;
use app\models\Sueldosempresas;
use app\models\PersonaMovimientoTipo;

class SiteController extends Controller
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
    /**
     * {@inheritdoc}
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {        
        if (Yii::$app->user->isGuest) {
            \Yii::$app->getUser()->logout();
            return $this->redirect(['site/login']);
        } else {
            return $this->render('index');
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        \Yii::$app->getUser()->logout();
        return $this->redirect(['site/login']);
    }

    public static function creaPadre($padre, $icono) {
        return '<li class="treeview"><a href="#"><i style="color: orange" class="fa '.$icono.' "></i>  <span>'.$padre.'</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
        <ul class="treeview-menu">';
    }

    public static function cierraPadre() {
        return '</ul>
        </li>';
    }

    public static function creaHijo($hijo, $icono, $url) {
        return '<li><a href="/index.php?r='.$url.'"><i class="fa '.$icono.'"></i>  <span>'.$hijo.'</span></a></li>';
    }

    static function getNombreMes($mes) {
        $nombreMes = 'Enero';
        if ($mes == 2) {$nombreMes = 'Febrero';}
        if ($mes == 3) {$nombreMes = 'Marzo';}
        if ($mes == 4) {$nombreMes = 'Abril';}
        if ($mes == 5) {$nombreMes = 'Mayo';}
        if ($mes == 6) {$nombreMes = 'Junio';}
        if ($mes == 7) {$nombreMes = 'Julio';}
        if ($mes == 8) {$nombreMes = 'Agosto';}
        if ($mes == 9) {$nombreMes = 'Septiembre';}
        if ($mes == 10) {$nombreMes = 'Octubre';}
        if ($mes == 11) {$nombreMes = 'Noviembre';}
        if ($mes == 12) {$nombreMes = 'Diciembre';}

        return $nombreMes;
    }

    public static function numeroToLetra ($numero) {
        $db = Yii::$app->db;

        $letra = $db->createCommand("select fn_numeroLetras(:numero) as salida;")
            ->bindValue(':numero', floatval($numero))
            ->queryOne();

        return $letra['salida'];
    }

    public function actionCambioPass()
    {
        return $this->render('cambio-pass');
    }

    public function actionCambioPassAccion($pass1)
    {
        $db = Yii::$app->db;

        $salida = 0;

        $db->createCommand("update user set password_hash = :pass
        where id = :id; ")
            ->bindValue(':id', Yii::$app->user->identity->id)
            ->bindValue(':pass', Yii::$app->getSecurity()->generatePasswordHash($pass1))
            ->execute();  

        $salida = 1;

        return $this->renderPartial('cambio-pass-accion',['salida' => $salida]);
    }

    public function actionCuenta($tipo)
    {
        $sucursales = Sueldosempresas::find()->orderBy("Empresa")->all();
        return $this->render('cuenta',['tipo' => $tipo, 'sucursales' => $sucursales]);
    }

    public function actionCuentaPersona($tipo, $fila, $sucursal)
    {
        $db = Yii::$app->db;
        $suc1 = $sucursal;
        $suc2 = $sucursal;
        if ($sucursal == 0) {$suc1 = 1; $suc2 = 9;}

        $personas = $db->createCommand("select p.id,concat(p.apellido,' ', p.nombre) nombrePersona,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end compra, 
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end pago,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end -
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end saldo
        from persona p 
        left join (select sum(m.importe) importe, m.id_persona 
        from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 1 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm1 on pm1.id_persona = p.id 
        left join (select sum(m.importe) importe, m.id_persona 
        from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 0 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm2 on pm2.id_persona = p.id 
        where p.id_tipo_persona = :tipo and pm1.importe > 0 or pm2.importe > 0
        group by p.id,p.apellido, p.nombre
        
        order by nombrePersona;")
        ->bindValue(':tipo', $tipo)
        ->bindValue(':suc1', $suc1)
        ->bindValue(':suc2', $suc2)
        ->queryAll();

        return $this->renderPartial('cuenta-persona',['personas' => $personas, 'tipo' => $tipo, 'fila' => $fila]);
    }

    public function actionCuentaLista($id, $sucursal)
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;

        $listado = self::getItemsCuenta($id, $sucursal);

        return $this->renderPartial('cuenta-lista',['listado' => $listado]);
    }

    public static function getItemsCuenta($id, $sucursal)
    {
        $db = Yii::$app->db;
        $listado = $db->createCommand("call itemsCuenta(:id, :sucursal);")
        ->bindValue(':id', $id)
        ->bindValue(':sucursal', $sucursal)
        ->queryAll();

        return $listado;
    }

    public function actionCuentaAjuste($id)
    {
        $db = Yii::$app->db;
        $sucursales = Sueldosempresas::find()->orderBy("Empresa")->all();
        $ajustes = PersonaMovimientoTipo::find()->where(['id' => [3,4]])->orderBy("movimiento")->all();

        return $this->renderPartial('cuenta-ajuste',['id' => $id, 'sucursales' => $sucursales, 'ajustes' => $ajustes]);
    }

    public function actionCuentaAjusteGuarda($id, $sucursal, $tipo, $importe, $obs)
    {
        $db = Yii::$app->db;
        $salida = 0;

        $db->createCommand("insert into persona_movimiento (id_persona, id_movimiento_tipo, importe, fecha, hora, id_usuario, id_empresa, obs) 
        values(:id_persona, :tipo, :importe, curdate(), curtime(), :id_usuario, :id_empresa, :obs)")
        ->bindValue(':id_persona', (int)$id)
        ->bindValue(':tipo', (int)$tipo)
        ->bindValue(':importe', floatval($importe))
        ->bindValue(':id_empresa', (int)$sucursal)
        ->bindValue(':id_usuario', Yii::$app->user->identity->id)
        ->bindValue(':obs', $obs)
        ->execute();

        $salida = 1;

        return $this->renderPartial('cuenta-ajuste-guarda',['salida' => $salida, 'id' => $id]);
    }

    public function actionCuentaImprime($id, $sucursal)
    {
        $db = Yii::$app->db;
        $suc1 = $sucursal;
        $suc2 = $sucursal;
        if ($sucursal == 0) {$suc1 = 1; $suc2 = 9;}

        $datos = $db->createCommand("select p.id,concat(p.apellido,' ', p.nombre) nombrePersona,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end compra, 
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end pago,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end -
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end saldo
        from persona p 
        left join (select sum(importe) importe, id_persona from persona_movimiento where id_movimiento_tipo in (1,3) and id_empresa between :suc1 and :suc2 group by id_persona)
        pm1 on pm1.id_persona = p.id 
        left join (select sum(importe) importe, id_persona from persona_movimiento where id_movimiento_tipo in (2,4) and id_empresa between :suc1 and :suc2 group by id_persona)
        pm2 on pm2.id_persona = p.id 
        where p.id = :id
        group by p.id, p.apellido, p.nombre
        order by nombrePersona;")
        ->bindValue(':id', $id)
        ->bindValue(':suc1', $suc1)
        ->bindValue(':suc2', $suc2)
        ->queryOne();

        $items = $db->createCommand("select pm.*, t.movimiento tipoMovimiento, pm.id_viaje,
        concat(u.apellido,' ',u.nombre) usuario,
        case when t.debe = 1 then pm.importe end debe, 
        case when t.debe = 0 then pm.importe end haber 
        from persona_movimiento pm
        join persona_movimiento_tipo t on t.id = pm.id_movimiento_tipo
        join user u on u.id = pm.id_usuario
        where pm.id_persona = :id and pm.id_empresa between :suc1 and :suc2 order by fecha desc, hora desc;")
        ->bindValue(':id', $id)
        ->bindValue(':suc1', $suc1)
        ->bindValue(':suc2', $suc2)
        ->queryAll();

        require_once('../vendor/tcpdf/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'Legal', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Estado de cuenta');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 5, 10, false);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage('P', 'Legal');
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetFont('helvetica', '', 10);

        $tbl = $this->renderPartial('_cuenta', ['datos' => $datos, 'items' => $items]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->SetFont('helvetica', '', 2);

        ob_end_clean();
        $pdf->Output('resumen_cuenta'.$id.'.pdf', 'I');
    }

    public function actionCuentaGralImprime($tipo, $sucursal)
    {
        $db = Yii::$app->db;
        $tipoPersona = 'CLIENTES';
        if ($tipo == 2) {$tipoPersona = 'PROVEEDORES';}
        if ($tipo == 3) {$tipoPersona = 'TRANSPORTISTAS';}

        $suc1 = $sucursal;
        $suc2 = $sucursal;
        if ($sucursal == 0) {$suc1 = 1; $suc2 = 9;}

        $personas = $db->createCommand("select p.id,concat(p.apellido,' ', p.nombre) nombrePersona,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end compra, 
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end pago,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end -
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end saldo
        from persona p 
        left join (select sum(m.importe) importe, m.id_persona from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 1 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm1 on pm1.id_persona = p.id 
        left join (select sum(m.importe) importe, m.id_persona from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 0 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm2 on pm2.id_persona = p.id 
        where p.id_tipo_persona = :tipo and pm1.importe > 0 or pm2.importe > 0
        group by p.id,p.apellido, p.nombre
        order by nombrePersona")
        ->bindValue(':tipo', $tipo)
        ->bindValue(':suc1', $suc1)
        ->bindValue(':suc2', $suc2)
        ->queryAll();

        require_once('../vendor/tcpdf/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'Legal', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Estado de cuenta');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 5, 10, false);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage('P', 'Legal');
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetFont('helvetica', '', 10);

        $tbl = $this->renderPartial('_cuenta_gral', ['personas' => $personas, 'tipoPersona' => $tipoPersona, 'sucursal' => $sucursal]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->SetFont('helvetica', '', 2);

        ob_end_clean();
        $pdf->Output('resumen_cuenta_general_'.$tipoPersona.'.pdf', 'I');
    }   
    
    public function actionCuentaGralExcel($tipo, $sucursal)
    {        
        $db = Yii::$app->db;
        $tipoPersona = 'CLIENTES';
        if ($tipo == 2) {$tipoPersona = 'PROVEEDORES';}
        if ($tipo == 3) {$tipoPersona = 'TRANSPORTISTAS';}

        $suc1 = $sucursal;
        $suc2 = $sucursal;
        if ($sucursal == 0) {$suc1 = 1; $suc2 = 9;}

        $personas = $db->createCommand("select p.id,concat(p.apellido,' ', p.nombre) nombrePersona,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end compra, 
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end pago,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end -
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end saldo
        from persona p 
        left join (select sum(m.importe) importe, m.id_persona from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 1 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm1 on pm1.id_persona = p.id 
        left join (select sum(m.importe) importe, m.id_persona from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 0 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm2 on pm2.id_persona = p.id 
        where p.id_tipo_persona = :tipo and pm1.importe > 0 or pm2.importe > 0
        group by p.id,p.apellido, p.nombre
        order by nombrePersona;")
        ->bindValue(':tipo', $tipo)
        ->bindValue(':suc1', $suc1)
        ->bindValue(':suc2', $suc2)
        ->queryAll();

        return $this->renderPartial('cuenta-gral-excel',['personas' => $personas, 'tipoPersona' => $tipoPersona, 'sucursal' => $sucursal]);
    }

    public function actionCuentaPersonaImprime($tipo, $sucursal)
    {
        $db = Yii::$app->db;
        $suc1 = $sucursal;
        $suc2 = $sucursal;
        if ($sucursal == 0) {$suc1 = 1; $suc2 = 9;}

        $personas = $db->createCommand("select p.id,concat(p.apellido,' ', p.nombre) nombrePersona,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end compra, 
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end pago,
        case when max(pm1.importe) is not null then max(pm1.importe) else 0 end -
        case when max(pm2.importe) is not null then max(pm2.importe) else 0 end saldo
        from persona p 
        left join (select sum(m.importe) importe, m.id_persona from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 1 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm1 on pm1.id_persona = p.id 
        left join (select sum(m.importe) importe, m.id_persona from persona_movimiento m
        join persona_movimiento_tipo t on t.id = m.id_movimiento_tipo
        where t.debe = 0 and m.id_empresa between :suc1 and :suc2 group by id_persona)
        pm2 on pm2.id_persona = p.id 
        where p.id_tipo_persona = :tipo and pm1.importe > 0 or pm2.importe > 0
        group by p.id,p.apellido, p.nombre
        order by nombrePersona;")
        ->bindValue(':tipo', $tipo)
        ->bindValue(':suc1', $suc1)
        ->bindValue(':suc2', $suc2)
        ->queryAll();

        require_once('../vendor/tcpdf/tcpdf.php');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('DEP');
        $pdf->SetTitle('Estado de cuenta');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(3, 3, 3, false);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('P', 'A4');
        $tbl = $this->renderPartial('_cuenta_persona', ['personas' => $personas, 'tipo' => $tipo, 'sucursal' => $sucursal]);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        ob_end_clean();
        $pdf->Output('cuenta_persona.pdf', 'I');
    }
}
