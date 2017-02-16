<?php

namespace app\controllers;

use Yii;
use app\models\Usuario;
use app\models\PostoUsuario;
use app\models\UsuarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;

class UsuarioController extends Controller {

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
                'only' => ['index', 'view', 'create', 'update', 'update-senha', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create'],
                        'allow' => Yii::$app->user->identity->tipo_usuario_id == 1 && $_GET['id'] == 2 || Yii::$app->user->identity->status == 2 && $this->allowID(),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['view', 'update-senha'],
                        'allow' => Yii::$app->user->identity->usuario_id == $_GET['id'] && $this->allowID(),
                        'roles' => ['@']
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => Yii::$app->user->identity->usuario_id == $_GET['id'] || Yii::$app->user->identity->tipo_usuario_id == 1 && $this->PostoUsuario() || Yii::$app->user->identity->status == 2 && $this->allowID(),
                        'roles' => ['@']
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => Yii::$app->user->identity->status == 2 && $this->allowID(),
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    public function PostoUsuario() {
        $usuarioID = $_GET['id'];
        $modelsPostoUsuario = PostoUsuario::findAll(['usuario_id' => $usuarioID]);
        foreach ($modelsPostoUsuario as $index => $modelPostoUsuario) {
            $postoIDS[$index] = $modelPostoUsuario->posto_id;
        }

        if ($modelsPostoUsuario) {
            if (in_array(Yii::$app->user->identity->posto_id, $postoIDS)) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    public function allowID() {
        if (Usuario::findOne(['usuario_id' => Yii::$app->user->identity->usuario_id])->status != 0) {
            return true;
        }
        Yii::$app->user->logout();
        return false;
    }

    public function actionIndex($id) {
        $searchModel = new UsuarioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post(), $id);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'id' => $id
        ]);
    }

    public function actionView($id) {
        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($id) {
        $model = new Usuario();
        $model->scenario = 'create';
        $model->tipo_usuario_id = $id;
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->posto_id = Yii::$app->user->identity->posto_id;
            $model->senha = sha1($model->senha);
            $model->hash = $this->hash($model->email);
            $model->save();

            $modelPostoUsuario = new PostoUsuario();
            $modelPostoUsuario->posto_id = $model->posto_id;
            $modelPostoUsuario->usuario_id = $model->usuario_id;
            $modelPostoUsuario->save();

            Yii::$app->session->setFlash('success', ['body' => '' . $model->tipoUsuario->descricao_singular . ' registrado com sucesso!']);
            return $this->redirect(['index', 'id' => $model->tipo_usuario_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->senha = sha1($model->senha);
            $model->hash = $this->hash($model->email);
            $model->save();

            Yii::$app->session->setFlash('success', ['body' => '' . $model->tipoUsuario->descricao_singular . ' alterado com sucesso!']);
            return $this->redirect(['index', 'id' => $model->tipo_usuario_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdateSenha($id) {
        $model = $this->findModel($id);

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        if ($model)
            $model->scenario = 'update-senha';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = 'json';
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->senha = sha1($model->nova_senha);
            $model->hash = $this->hash($model->email);
            $model->save(false);

            Yii::$app->session->setFlash('success', ['body' => 'Senha alterada com sucesso!']);
            return $this->redirect(['view', 'id' => $model->usuario_id]);
        } else {
            return $this->render('update-senha', [
                        'model' => $model
            ]);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save();

        Yii::$app->session->setFlash('success', ['body' => '' . $model->tipoUsuario->descricao_singular . ' excluído com sucesso!']);
        return $this->redirect(['index', 'id' => $model->tipo_usuario_id]);
    }

    public function hash($login) {
        $cost = 10;
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        $salt = sprintf("$2a$%02d$", $cost) . $salt;
        $hash = crypt($login, $salt);
        $chars = ["/", "?", "=", "'", "\"", "&", "."];
        $hash = str_replace($chars, "", $hash);
        return substr($hash, $cost, strlen($hash));
    }

    protected function findModel($id) {
        if (($model = Usuario::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
