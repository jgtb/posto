<?php

namespace app\controllers;

use Yii;
use app\models\CaminhaoCliente;
use app\models\Usuario;
use app\models\ProdutoNegociacao;
use app\models\CaminhaoClienteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CaminhaoClienteController extends Controller {

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
        $searchModel = new CaminhaoClienteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        $this->layout = 'caminhao';

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate() {
        $model = new CaminhaoCliente();
        $model->scenario = 'create';
        $model->produto_negociacao_id = 0;
        $model->caminhao_id = 1;
        $model->status = 1;

        $this->layout = 'caminhao';
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->save();
            
            Yii::$app->session->setFlash('success', ['body' => 'Aluguel registrado com sucesso!']);
            return $this->redirect(['index']);
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

        $this->layout = 'caminhao';
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->save();

            if (in_array($model->cliente_id, [1, 2])) {
                $modelProdutoNegociacao = ProdutoNegociacao::findOne(['produto_negociacao_id' => $model->produto_negociacao_id]);
                $modelProdutoNegociacao->valor = $model->valor_litro;
                $modelProdutoNegociacao->qtde = $model->valor_carrada;
                $modelProdutoNegociacao->nota_fiscal = $model->nota_fiscal;
                $modelProdutoNegociacao->data = $model->data;
                $modelProdutoNegociacao->observacao = $model->observacao;
                $modelProdutoNegociacao->save();
            }

            Yii::$app->session->setFlash('success', ['body' => 'Aluguel alterado com sucesso!']);
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

        Yii::$app->session->setFlash('success', ['body' => 'Aluguel excluído com sucesso!']);
        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = CaminhaoCliente::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
