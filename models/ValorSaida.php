<?php

namespace app\models;

use Yii;

class ValorSaida extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'valor_saida';
    }

    public function rules()
    {
        return [
            [['bico_registro_id', 'produto_negociacao_id'], 'required'],
            [['bico_registro_id', 'produto_negociacao_id'], 'integer'],
            [['valor'], 'number'],
            [['bico_registro_id'], 'exist', 'skipOnError' => true, 'targetClass' => BicoRegistro::className(), 'targetAttribute' => ['bico_registro_id' => 'bico_registro_id']],
            [['produto_negociacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdutoNegociacao::className(), 'targetAttribute' => ['produto_negociacao_id' => 'produto_negociacao_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'valor_saida_id' => 'Valor Saida',
            'bico_registro_id' => 'Bico Registro ID',
            'produto_negociacao_id' => 'Produto Negociacao ID',
            'valor' => 'Valor',
        ];
    }

    public function getBicoRegistro()
    {
        return $this->hasOne(BicoRegistro::className(), ['bico_registro_id' => 'bico_registro_id']);
    }

    public function getProdutoNegociacao()
    {
        return $this->hasOne(ProdutoNegociacao::className(), ['produto_negociacao_id' => 'produto_negociacao_id']);
    }
}
