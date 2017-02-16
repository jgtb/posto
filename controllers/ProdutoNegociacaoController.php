<?php

namespace app\controllers;

use Yii;
use app\models\ProdutoNegociacao;
use app\models\Usuario;
use app\models\PostoUsuario;
use app\models\ProdutoNegociacaoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

class ProdutoNegociacaoController extends Controller {

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
                        'allow' => PostoUsuario::findOne(['posto_id' => ProdutoNegociacao::findOne(['produto_negociacao_id' => $_GET['id']])->posto_id, 'usuario_id' => Yii::$app->user->identity->usuario_id]) && $this->allowID(),
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
        $searchModel = new ProdutoNegociacaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post(), $id);

        $this->layout = 'main';

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'id' => $id,
        ]);
    }

    public function actionCreate($id) {
        $model = new ProdutoNegociacao();
        $model->scenario = 'create';
        $model->posto_id = Yii::$app->user->identity->posto_id;
        $model->negociacao_id = $id;
        $model->valor = 0;
        $model->produto_id = $id == 1 ? 3 : '';
        $model->status = 1;

        $this->layout = 'main';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = 'json';
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->save();

            Yii::$app->session->setFlash('success', ['body' => '' . substr($model->negociacao->descricao, 0, -1) . ' registrada com sucesso!']);
            return $this->redirect(['index', 'id' => $model->negociacao_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $model->data = date('d/m/Y', strtotime($model->data));

        $this->layout = 'main';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = 'json';
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->save();

            Yii::$app->session->setFlash('success', ['body' => '' . substr($model->negociacao->descricao, 0, -1) . ' alterada com sucesso!']);
            return $this->redirect(['index', 'id' => $model->negociacao_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);

        if ($model->checaEstoqueDeleteCompra()) {
            Yii::$app->session->setFlash('success', ['body' => '' . substr($model->negociacao->descricao, 0, -1) . ' excluída com sucesso!']);
            $model->status = 0;
            $model->save();
        }

        return $this->redirect(['index', 'id' => $model->negociacao_id]);
    }

    protected function findModel($id) {
        if (($model = ProdutoNegociacao::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
