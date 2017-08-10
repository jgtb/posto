<?php

namespace app\models;

use Yii;

class Posto extends \yii\db\ActiveRecord {

    public $posto_usuario;

    public static function tableName() {
        return 'posto';
    }

    public function rules() {
        return [
            [['descricao'], 'required', 'message' => 'Campo obrigatório'],
            [['status'], 'integer'],
            [['descricao'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'posto_id' => 'Posto ID',
            'descricao' => 'Descrição',
            'status' => 'Situação',
        ];
    }

    public function getBombas() {
        return $this->hasMany(Bomba::className(), ['posto_id' => 'posto_id']);
    }

    public function getDespesas() {
        return $this->hasMany(Despesa::className(), ['posto_id' => 'posto_id']);
    }

    public function getPostoUsuarios() {
        return $this->hasMany(PostoUsuario::className(), ['posto_id' => 'posto_id']);
    }

    public function getProdutos() {
        return $this->hasMany(Produto::className(), ['posto_id' => 'posto_id']);
    }

    public function getRegistros() {
        return $this->hasMany(Registro::className(), ['posto_id' => 'posto_id']);
    }
    
    public function getAllUsuario() {
        $modelsUsuario = Usuario::find()->where(['tipo_usuario_id' => 1])->andWhere(['>', 'status', 0])->all();
        foreach ($modelsUsuario as $modelUsuario) {
            $arr[] = $modelUsuario->usuario_id;
        }

        return $arr;
    }

    public function getCurrentUsuario() {
        $modelsPostoUsuario = PostoUsuario::findAll(['posto_id' => $this->posto_id]);
        foreach ($modelsPostoUsuario as $modelPostoUsuario) {
            $arr[] = $modelPostoUsuario->usuario_id;
        }

        return $arr;
    }

}
