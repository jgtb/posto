<?php

namespace app\models;

use Yii;

class TipoCombustivel extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'tipo_combustivel';
    }
    
    public function rules()
    {
        return [
            [['descricao'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipo_combustivel_id' => 'Tipo Combustivel',
            'descricao' => 'Descricao',
        ];
    }

    public function getBicos()
    {
        return $this->hasMany(Bico::className(), ['tipo_combustivel_id' => 'tipo_combustivel_id']);
    }

    public function getCaminhaoClientes()
    {
        return $this->hasMany(CaminhaoCliente::className(), ['tipo_combustivel_id' => 'tipo_combustivel_id']);
    }
}
