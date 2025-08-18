<?php

namespace app\controllers;

use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $config = [
            'pageParam' => 'page',
            'pageSizeParam' => 'per-page',
            'forcePageParam' => true,
            'route' => null,
            'params' => null,
            'urlManager' => null,
            'validatePage' => true,
            'totalCount' => 5214,
            'defaultPageSize' => 20,
            'pageSizeLimit' => [
                '0' => 1,
                '1' => 50
            ],
            'pagesize' => 15
        ];
        $dataProvider->setPagination($config);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionImgPerfil()
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $idUsuario = (int)$request->post('idUsuario');

        if (file_exists($_FILES['file']['tmp_name'])) {
            $fileError = $_FILES['file']['error'];

            if ($fileError == UPLOAD_ERR_OK) {
                //$destino = '/home/consultora.dp.8/gutierrez/web/images/usr_img';
               $destino = '/Users/danielponce/Gutierrez/gutierrez/web/images/usr_img';
                $pdf_info = explode(".", $_FILES['file']['name']);
                $tipo = end($pdf_info);
                $archivo = 'usr_img_' . $_FILES ['file']['name'];

                if($tipo == "png" || $tipo == "jpeg" || $tipo == "jpg" || $tipo == "bmp" || $tipo == "BMP" || $tipo == "JPG" || $tipo == "JPEG"){
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . '/' . $archivo)) {
                        $db->createCommand("update user set img = :img where id = :id")
                        ->bindValue(':img', $archivo)
                        ->bindValue(':id', $idUsuario)
                        ->execute();
                    } else {
                        echo "Error el archivo no fue subido.";
                    }
                }
            }
        }
        return $this->renderPartial('img-perfil',['idUsuario' => $idUsuario]);            
    }
}
