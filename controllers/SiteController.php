<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Usuario;
use app\models\Posto;
use app\models\Relatorio;
use app\models\PostoUsuarioSearch;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        $modelsPosto = Posto::find()->all();

        return $this->render('index', ['modelsPosto' => $modelsPosto]);
    }

    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'main';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionMeusPostos() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

        $this->layout = Yii::$app->user->identity->posto_id != 0 ? 'main' : 'caminhao';

        $searchModelPostoUsuario = new PostoUsuarioSearch();
        $dataProviderPostoUsuario = $searchModelPostoUsuario->search(Yii::$app->request->post());

        return $this->render('meus-postos', [
                    'dataProviderPostoUsuario' => $dataProviderPostoUsuario
        ]);
    }

    public function actionTrocaPosto($id) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

        $modelUsuario = Usuario::findOne(['usuario_id' => Yii::$app->user->identity->usuario_id]);

        if ($id != 0) {
            $modelUsuario->posto_id = $id;
            $modelUsuario->caminhao_id = 0;
        } else {
            $modelUsuario->caminhao_id = $id;
            $modelUsuario->posto_id = 0;
        }

        $modelUsuario->save();

        Yii::$app->session->setFlash('success', ['body' => $id != 0 ? 'Você está gerenciando o Posto #' . Posto::findOne(['posto_id' => $id])->descricao : 'Você está gerenciando #Carro Tanque']);
        return $this->redirect(['index']);
    }

    public function actionRedefine() {
        $model = new Usuario();
        $model->scenario = 'redefine';

        $this->layout = 'main';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model = Usuario::findOne(['email' => $model->email]);
            $email = Yii::$app->mailer->compose()
                    ->setFrom('jgtb313@gmail.com')
                    ->setTo($model->email)
                    ->setSubject('My Company')
                    ->setHtmlBody('My Company');

            Yii::$app->session->setFlash('success', ['body' => 'E-mail enviado para ' . $model->email]);
            return $this->refresh();
        }

        return $this->render('redefine', ['model' => $model]);
    }

    public function actionAltera($hash) {
        $model = Usuario::findOne(['hash' => $hash]);

        $this->layout = 'main';

        $model->scenario = 'altera';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->senha = sha1($model->nova_senha);
            $model->hash = $this->hash($model->email);
            $model->save();

            Yii::$app->session->setFlash('success', ['body' => 'Senha alterada com sucesso']);
            return $this->redirect(['login']);
        }

        return $this->render('altera', ['model' => $model]);
    }

    public function actionRelatorio() {
        if (Yii::$app->user->isGuest || Yii::$app->user->identity->tipo_usuario == 2) {
            return $this->redirect(['login']);
        }

        $model = new Relatorio();

        return $this->render('relatorio', ['model' => $model]);
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

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

}
