<?php

namespace app\controllers;

use Yii;
use app\models\Relatorio;
use app\models\Usuario;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class RelatorioController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => Yii::$app->user->identity->tipo_usuario_id == 1 && $this->allowID(),
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
    
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render($model->getRelatorioURL(), ['model' => $model]);
    }

    public function actionCreate() {
        $model = new Relatorio();

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->data_inicial = date('Y-m-d', strtotime(str_replace('/', '-', $model->data)));
            $model->data_final = date('Y-m-d', strtotime(str_replace('/', '-', $model->data_final)));
            $model->save();

            return $this->redirect(['view', 'id' => $model->relatorio_id]);
        } else {
            return $this->render('index', [
                        'model' => $model,
            ]);
        }
    }

    protected function findModel($id) {
        if (($model = Relatorio::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
