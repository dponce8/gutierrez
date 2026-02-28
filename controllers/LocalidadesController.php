<?php

namespace app\controllers;

use app\models\Localidades;
use app\models\LocalidadesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LocalidaesController implements the CRUD actions for Localidades model.
 */
class LocalidadesController extends Controller
{
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
     * Lists all Localidades models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LocalidadesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Localidades model.
     * @param int $IdLocalidad Id Localidad
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($IdLocalidad)
    {
        return $this->render('view', [
            'model' => $this->findModel($IdLocalidad),
        ]);
    }

    /**
     * Creates a new Localidades model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Localidades();
        $popup = $this->request->get('popup');

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                if ($this->request->post('popup')) {
                    return $this->redirect(['created-popup', 'IdLocalidad' => $model->IdLocalidad]);
                }
                return $this->redirect(['view', 'IdLocalidad' => $model->IdLocalidad]);
            }
        } else {
            $model->loadDefaultValues();
        }

        if ($this->request->get('popup')) {
            return $this->renderPartial('create-form', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Vista mostrada al crear una localidad desde popup (ej. desde presupuesto origen/destino).
     */
    public function actionCreatedPopup($IdLocalidad)
    {
        return $this->renderPartial('created-popup', ['IdLocalidad' => $IdLocalidad]);
    }

    /**
     * Updates an existing Localidades model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $IdLocalidad Id Localidad
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($IdLocalidad)
    {
        $model = $this->findModel($IdLocalidad);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'IdLocalidad' => $model->IdLocalidad]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Localidades model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $IdLocalidad Id Localidad
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($IdLocalidad)
    {
        $this->findModel($IdLocalidad)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Localidades model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $IdLocalidad Id Localidad
     * @return Localidades the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($IdLocalidad)
    {
        if (($model = Localidades::findOne(['IdLocalidad' => $IdLocalidad])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
