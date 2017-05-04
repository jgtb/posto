<?php

namespace app\controllers;

use Yii;
use app\models\ValorCombustivel;
use app\models\Usuario;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ValorCombustivelController extends Controller {

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
                'only' => ['update'],
                'rules' => [
                    [
                        'actions' => ['update'],
                        'allow' => Yii::$app->user->identity->usuario_id == 1 && $this->allowID(),
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

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', ['body' => 'Manutenção alterada com sucesso']);
            return $this->redirect(['update', 'id' => $model->valor_combustivel_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    protected function findModel($id) {
        if (($model = ValorCombustivel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
