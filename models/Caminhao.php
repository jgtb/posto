<?php

namespace app\models;

use Yii;

class Caminhao extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'caminhao';
    }

    public function rules()
    {
        return [
            [['descricao', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['status'], 'integer'],
            [['descricao'], 'string', 'max' => 225],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'caminhao_id' => 'Caminhao ID',
            'descricao' => 'Descrição',
            'status' => 'Situação',
        ];
    }

    public function getCaminhaoClientes()
    {
        return $this->hasMany(CaminhaoCliente::className(), ['caminhao_id' => 'caminhao_id']);
    }
}
