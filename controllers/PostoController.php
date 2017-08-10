<?php

namespace app\controllers;

use Yii;
use app\models\Posto;
use app\models\Cliente;
use app\models\Usuario;
use app\models\PostoUsuario;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class PostoController extends Controller {

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
                'only' => ['create', 'update'],
                'rules' => [
                    [
                        'actions' => ['create', 'update'],
                        'allow' => Yii::$app->user->identity->status == 2,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate() {
        $model = new Posto();
        $model->status = 1;

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $modelCliente = new Cliente();
            $modelCliente->posto_id = $model->posto_id;
            $modelCliente->nome = $model->descricao;
            $modelCliente->status = 1;
            $modelCliente->save();

            $modelsUsuario = Usuario::findAll(['status' => 2]);
            foreach ($modelsUsuario as $modelUsuario) {
                $modelPostoUsuario = new PostoUsuario();
                $modelPostoUsuario->posto_id = $model->posto_id;
                $modelPostoUsuario->usuario_id = $modelUsuario->usuario_id;
                $modelPostoUsuario->save();
            }

            Yii::$app->session->setFlash('success', ['body' => 'Posto registrado com sucesso!']);
            return $this->redirect(['site/meus-postos']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', ['body' => 'Posto alterado com sucesso!']);
            return $this->redirect(['site/meus-postos']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    protected function findModel($id) {
        if (($model = Posto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
