<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\controllers\RecalculoController;
use TCPDF;

class SiteController extends Controller
{
    public $enableCsrfValidation = false;
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
}
