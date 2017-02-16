<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;
use Yii;

class Usuario extends ActiveRecord implements IdentityInterface {

    public $id;
    public $username;
    public $password;
    public $auth_key;
    public $accessToken;
    public $password_hash;
    public $password_reset_token;
    public $nova_senha;
    public $senha_antiga;
    public $confirma_senha;

    public static function tableName() {
        return 'usuario';
    }

    public function rules() {
        return [
            [['tipo_usuario_id', 'nome', 'email', 'senha', 'status'], 'required', 'on' => ['create', 'update'], 'message' => 'Campo obrigatório'],
            //SCENARIO ALTERA
            [['nova_senha', 'confirma_senha'], 'required', 'on' => 'altera', 'message' => 'Campo obrigatório'],
            ['confirma_senha', 'compare', 'compareAttribute' => 'nova_senha', 'on' => 'altera', 'message' => 'As senhas não correspondem'],
            //SCENARIO UPDATE-SENHA
            [['senha_antiga', 'nova_senha', 'confirma_senha'], 'required', 'on' => ['update-senha'], 'message' => 'Campo obrigatório'],
            [['senha_antiga'], 'checaSenhaAntiga', 'on' => ['update-senha'], 'message' => 'Senha inválida'],
            [['confirma_senha'], 'compare', 'compareAttribute' => 'nova_senha', 'operator' => '==', 'on' => ['update-senha'], 'message' => 'As senhas não correspondem'],
            //SCENARIO REDEFINE
            ['email', 'required', 'on' => 'redefine', 'message' => 'Campo obrigatório'],
            ['email', 'exist', 'on' => 'redefine', 'message' => 'E-mail não registrado'],
            [['usuario_id', 'tipo_usuario_id', 'posto_id', 'caminhao_id', 'status'], 'integer'],
            [['nome', 'email', 'hash'], 'string', 'max' => 255],
            [['senha'], 'string', 'max' => 45],
            [['tipo_usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoUsuario::className(), 'targetAttribute' => ['tipo_usuario_id' => 'tipo_usuario_id']],
        ];
    }
    
    public function checaSenhaAntiga($attribute, $params) {
        if ($this->senha != sha1($this->senha_antiga))
            $this->addError($attribute, 'Senha inválida');
    }

    public function attributeLabels() {
        return [
            'usuario_id' => 'Usuario ID',
            'tipo_usuario_id' => 'Qualificação',
            'posto_id' => 'Posto',
            'nome' => 'Nome',
            'email' => 'Login ou E-mail',
            'senha' => 'Senha',
            'hash' => 'Hash',
            'status' => 'Situação',
        ];
    }

    public function getPostoUsuarios() {
        return $this->hasMany(PostoUsuario::className(), ['usuario_id' => 'usuario_id']);
    }

    public function getTipoUsuario() {
        return $this->hasOne(TipoUsuario::className(), ['tipo_usuario_id' => 'tipo_usuario_id']);
    }

    public static function findIdentity($id) {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    public static function findByUsername($username) {
        return static::findOne(['email' => $username]);
    }

    public static function findByPasswordResetToken($token) {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token
        ]);
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password) {
        return $this->senha === sha1($password) && $this->status != 0;
    }

    public function setPassword($password) {
        $this->password_hash = Security::generatePasswordHash($password);
    }

    public function generateAuthKey() {
        $this->auth_key = Security::generateRandomKey();
    }

    public function generatePasswordResetToken() {
        $this->password_reset_token = Security::generateRandomKey() . '_' . time();
    }

    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }
    
    public function getValorCombustivel($tipoCombustivelID)
    {
        $modelValorCombustivel = ValorCombustivel::findOne(['valor_combustivel_id' => Yii::$app->user->identity->posto_id]);
        return $tipoCombustivelID == 1 ? $modelValorCombustivel->valor_gasolina : $modelValorCombustivel->valor_diesel;
    }
    
    public function getPosto()
    {
        return Posto::findOne(['posto_id' => Yii::$app->user->identity->posto_id])->descricao;
    }

}
