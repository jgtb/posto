<?php

namespace app\models;

use Yii;

class ValorCombustivel extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'valor_combustivel';
    }

    public function rules()
    {
        return [
            [['valor_gasolina', 'valor_diesel'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'valor_combustivel_id' => 'Valor Combustivel ID',
            'valor_gasolina' => 'Valor da Gasolina',
            'valor_diesel' => 'Valor do Diesel',
        ];
    }
}
