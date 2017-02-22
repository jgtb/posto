<?php

namespace app\controllers;

use Yii;
use app\models\Despesa;
use app\models\Usuario;
use app\models\PostoUsuario;
use app\models\DespesaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class DespesaController extends Controller {

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
                        'allow' => PostoUsuario::findOne(['posto_id' => Despesa::findOne(['despesa_id' => $_GET['id']])->posto_id, 'usuario_id' => Yii::$app->user->identity->usuario_id]) && $this->allowID(),
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

    public function actionIndex($id) {
        $searchModel = new DespesaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post(), $id);

        $this->layout = $id == 1 ? 'main' : 'caminhao';

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'id' => $id,
        ]);
    }

    public function actionCreate($id) {
        $model = new Despesa();
        $model->produto_negociacao_id = 0;
        $model->referencial = $id;
        $model->posto_id = $id == 1 ? Yii::$app->user->identity->posto_id : 1;
        $model->status = 1;

        $this->layout = $id == 1 ? 'main' : 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data_vencimento = date('Y-m-d', strtotime(str_replace('/', '-', $model->data_vencimento)));
            $model->data_pagamento = $model->data_pagamento != NULL ? date('Y-m-d', strtotime(str_replace('/', '-', $model->data_vencimento))) : NULL;
            $model->save();

            Yii::$app->session->setFlash('success', ['body' => 'Despesa registrada com sucesso!']);
            return $this->redirect(['index', 'id' => $model->referencial]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->data_vencimento = date('d/m/Y', strtotime($model->data_vencimento));
        $model->data_pagamento = $model->data_pagamento != NULL ? date('d/m/Y', strtotime($model->data_pagamento)) : NULL;

        $this->layout = $model->referencial == 1 ? 'main' : 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data_vencimento = date('Y-m-d', strtotime(str_replace('/', '-', $model->data_vencimento)));
            $model->data_pagamento = $model->data_pagamento != NULL ? date('Y-m-d', strtotime(str_replace('/', '-', $model->data_pagamento))) : NULL;
            $model->save();

            Yii::$app->session->setFlash('success', ['body' => 'Despesa alterada com sucesso!']);
            return $this->redirect(['index', 'id' => $model->referencial]);
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

        Yii::$app->session->setFlash('success', ['body' => 'Despesa excluída com sucesso!']);
        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = Despesa::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
