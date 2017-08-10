<?php

namespace app\controllers;

use Yii;
use app\models\Bomba;
use app\models\Usuario;
use app\models\PostoUsuario;
use app\models\BombaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class BombaController extends Controller {

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
                        'actions' => ['index', 'create'],
                        'allow' => $this->allowID(),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update', 'delete'],
                        'allow' => PostoUsuario::findOne(['posto_id' => Bomba::findOne(['bomba_id' => $_GET['id']])->posto_id, 'usuario_id' => Yii::$app->user->identity->usuario_id]) && $this->allowID(),
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
        $searchModel = new BombaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        
        $this->layout = 'main';
        
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate() {
        $model = new Bomba();
        $model->posto_id = Yii::$app->user->identity->posto_id;
        $model->status = 1;
        
        $this->layout = 'main';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', ['body' => 'Bomba registrada com sucesso!']);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        
        $this->layout = 'main';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', ['body' => 'Bomba alterada com sucesso!']);
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

        Yii::$app->session->setFlash('success', ['body' => 'Bomba excluída com sucesso!']);
        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = Bomba::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
