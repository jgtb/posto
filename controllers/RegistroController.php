<?php

namespace app\controllers;

use Yii;
use app\models\Registro;
use app\models\BicoRegistro;
use app\models\Bico;
use app\models\Usuario;
use app\models\PostoUsuario;
use app\models\ValorCombustivel;
use app\models\RegistroSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class RegistroController extends Controller {

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
                'only' => ['index', 'create', 'update', 'retorno', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create'],
                        'allow' => $this->allowID(),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update', 'retorno', 'delete'],
                        'allow' => PostoUsuario::findOne(['posto_id' => Registro::findOne(['registro_id' => $_GET['id']])->posto_id, 'usuario_id' => Yii::$app->user->identity->usuario_id]) && $this->allowID(),
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
        $searchModel = new RegistroSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        $this->layout = 'main';

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate() {
        $model = new Registro();
        $model->posto_id = Yii::$app->user->identity->posto_id;
        $model->status = 1;

        $this->layout = 'main';

        $modelsBico = Bico::find()
                        ->joinWith('bomba')
                        ->where(['bomba.posto_id' => $model->posto_id, 'bico.status' => 1, 'bomba.status' => 1])->all();

        foreach ($modelsBico as $index => $modelBico) {
            $modelsBicoRegistro[$index] = new BicoRegistro();
            $modelsBicoRegistro[$index]->bico_id = $modelBico->bico_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->data_sistema = date('Y-m-d H:i:s');
            $model->save();

            $postBicoRegistro = $_POST['BicoRegistro'];
            if ($postBicoRegistro) {
                foreach ($postBicoRegistro as $bicoID => $bicoRegistro) {
                    $modelBicoRegistro = new BicoRegistro();
                    $modelBicoRegistro->registro_id = $model->registro_id;
                    $modelBicoRegistro->bico_id = $bicoID;
                    $modelBicoRegistro->valor = Bico::findOne(['bico_id' => $bicoID])->tipo_combustivel_id == 1 ? ValorCombustivel::findOne(['valor_combustivel_id' => Yii::$app->user->identity->posto_id])->valor_gasolina : ValorCombustivel::findOne(['valor_combustivel_id' => Yii::$app->user->identity->posto_id])->valor_diesel;
                    $modelBicoRegistro->registro_atual = $bicoRegistro['registro_atual'];
                    $modelBicoRegistro->registro_anterior = $bicoRegistro['registro_anterior'];
                    $modelBicoRegistro->retorno = 0;
                    $modelBicoRegistro->status = 1;
                    $modelBicoRegistro->save();
                    $modelBicoRegistro->setSaldoValor();
                }
            }

            Yii::$app->session->setFlash('success', ['body' => 'Registro registrado com sucesso']);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'modelsBicoRegistro' => $modelsBicoRegistro,
                        'estoque' => $model->getEstoque()
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->data = date('d/m/Y', strtotime($model->data));

        $this->layout = 'main';

        $modelsBicoRegistro = BicoRegistro::findAll(['registro_id' => $model->registro_id]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->save();
            
            Yii::$app->session->setFlash('success', ['body' => 'Registro alterado com sucesso']);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
                        'modelsBicoRegistro' => $modelsBicoRegistro,
                        'estoque' => $model->getEstoque()
            ]);
        }
    }

    public function actionRetorno($id) {
        $model = $this->findModel($id);
        $model->data = date('d/m/Y', strtotime($model->data));

        $this->layout = 'main';

        $modelsBicoRegistro = BicoRegistro::findAll(['registro_id' => $model->registro_id]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->deleteBicoRegistros();

            $postBicoRegistro = $_POST['BicoRegistro'];
            if ($postBicoRegistro) {
                foreach ($postBicoRegistro as $bicoID => $bicoRegistro) {
                    $modelBicoRegistro = new BicoRegistro();
                    $modelBicoRegistro->registro_id = $model->registro_id;
                    $modelBicoRegistro->bico_id = $bicoID;
                    $modelBicoRegistro->valor = $bicoRegistro['valor'];
                    $modelBicoRegistro->registro_atual = $bicoRegistro['registro_atual'];
                    $modelBicoRegistro->registro_anterior = $bicoRegistro['registro_anterior'];
                    $modelBicoRegistro->retorno = $bicoRegistro['retorno'];
                    $modelBicoRegistro->status = 1;
                    $modelBicoRegistro->save();
                    $modelBicoRegistro->setSaldoValor();
                }
            }

            Yii::$app->session->setFlash('success', ['body' => 'Retorno registrado com sucesso']);
            return $this->redirect(['index']);
        } else {
            return $this->render('retorno', [
                        'model' => $model,
                        'modelsBicoRegistro' => $modelsBicoRegistro,
            ]);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->deleteRegistros();
        
        Yii::$app->session->setFlash('success', ['body' => 'Registro excluído com sucesso!']);
        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = Registro::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
