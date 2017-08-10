<?php

namespace app\controllers;

use Yii;
use app\models\Caminhao;
use app\models\Usuario;
use app\models\CaminhaoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CaminhaoController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => $this->allowID(),
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function allowID() {
        if (Usuario::findOne(['usuario_id' => Yii::$app->user->identity->usuario_id])->status != 0) {
            return true;
        }
        
        Yii::$app->user->logout();
        return false;
    }

    public function actionIndex() {
        $searchModel = new CaminhaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        
        $this->layout = 'caminhao';

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate() {
        $model = new Caminhao();
        $model->status = 1;
        
        $this->layout = 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', ['body' => 'Caminhão registrado com sucesso!']);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        
        $this->layout = 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', ['body' => 'Caminhão alterado com sucesso!']);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save();

        Yii::$app->session->setFlash('success', ['body' => 'Caminhão excluído com sucesso!']);
        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = Caminhao::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
