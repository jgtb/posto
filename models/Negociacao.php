<?php

namespace app\models;

use Yii;

class Negociacao extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'negociacao';
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
            'negociacao_id' => 'Negociacao ID',
            'descricao' => 'Descricao',
        ];
    }

    public function getProdutoNegociacaos()
    {
        return $this->hasMany(ProdutoNegociacao::className(), ['negociacao_id' => 'negociacao_id']);
    }
}
