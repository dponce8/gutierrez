<?php

namespace app\controllers;

use app\models\Concepto;
use app\models\ConceptoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ConceptoController implements the CRUD actions for Concepto model.
 */
class ConceptoController extends Controller
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
     * Lists all Concepto models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ConceptoSearch();
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
     * Displays a single Concepto model.
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
     * Creates a new Concepto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Concepto();

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
     * Updates an existing Concepto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        // Verificar si es un concepto de sistema
        if ($model->sistema != 0) {
            throw new NotFoundHttpException('No se puede editar un concepto de sistema.');
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Concepto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Verificar si es un concepto de sistema
        if ($model->sistema != 0) {
            throw new NotFoundHttpException('No se puede eliminar un concepto de sistema.');
        }
        
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Concepto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Concepto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Concepto::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
