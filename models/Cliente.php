<?php

namespace app\models;

use Yii;

class Cliente extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'cliente';
    }

    public function rules()
    {
        return [
            [['nome', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['posto_id', 'status'], 'integer'],
            [['nome'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'cliente_id' => 'Cliente ID',
            'posto_id' => 'Meu Posto?',
            'nome' => 'Descrição',
            'status' => 'Situação',
        ];
    }

    public function getCaminhaoClientes()
    {
        return $this->hasMany(CaminhaoCliente::className(), ['cliente_id' => 'cliente_id']);
    }
}
