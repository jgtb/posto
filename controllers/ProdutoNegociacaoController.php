<?php

namespace app\controllers;

use Yii;
use app\models\ProdutoNegociacao;
use app\models\Usuario;
use app\models\PostoUsuario;
use app\models\CaminhaoCliente;
use app\models\ProdutoNegociacaoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
        $model->valor_frete = 0;
        $model->produto_id = $id == 1 ? 3 : '';
        $model->status = 1;

        $this->layout = 'main';
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->save();

            if ($model->negociacao_id == 2) {
                $modelCaminhaoCliente = new CaminhaoCliente();
                $modelCaminhaoCliente->caminhao_id = 1;
                $modelCaminhaoCliente->cliente_id = Yii::$app->user->identity->posto_id;
                $modelCaminhaoCliente->tipo_combustivel_id = $model->produto_id;
                $modelCaminhaoCliente->produto_negociacao_id = $model->produto_negociacao_id;
                $modelCaminhaoCliente->valor_litro = $model->valor;
                $modelCaminhaoCliente->valor_carrada = $model->qtde;
                $modelCaminhaoCliente->valor_frete = $model->valor_frete;
                $modelCaminhaoCliente->nota_fiscal = $model->nota_fiscal;
                $modelCaminhaoCliente->data = $model->data;
                $modelCaminhaoCliente->observacao = $model->observacao;
                $modelCaminhaoCliente->status = 1;
                $modelCaminhaoCliente->save();
            }

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
        $model->valor_frete = $model->negociacao_id == 2 ? CaminhaoCliente::findOne(['produto_negociacao_id' => $model->produto_negociacao_id])->valor_frete : 0;

        $this->layout = 'main';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->save();

            if ($model->negociacao_id == 2) {
                $modelCaminhaoCliente = CaminhaoCliente::findOne(['produto_negociacao_id' => $model->produto_negociacao_id]);
                $modelCaminhaoCliente->valor_litro = $model->valor;
                $modelCaminhaoCliente->valor_carrada = $model->qtde;
                $modelCaminhaoCliente->valor_frete = $model->valor_frete;
                $modelCaminhaoCliente->nota_fiscal = $model->nota_fiscal;
                $modelCaminhaoCliente->data = $model->data;
                $modelCaminhaoCliente->observacao = $model->observacao;
                $modelCaminhaoCliente->save();
            }

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
        } else {
            Yii::$app->session->setFlash('danger', ['body' => 'Não foi possível excluír esta ' . substr($model->negociacao->descricao, 0, -1)]);
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
