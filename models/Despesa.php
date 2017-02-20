<?php

namespace app\models;

use Yii;

class Despesa extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'despesa';
    }

    public function rules()
    {
        return [
            [['posto_id', 'tipo_despesa_id', 'produto_negociacao_id', 'valor', 'data_vencimento', 'referencial', 'status'], 'required', 'message' => 'Campo Obrigatório'],
            [['posto_id', 'tipo_despesa_id', 'produto_negociacao_id', 'referencial', 'status'], 'integer'],
            [['valor'], 'number'],
            [['data_vencimento', 'data_pagamento'], 'safe'],
            [['observacao'], 'string', 'max' => 500],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
            [['tipo_despesa_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDespesa::className(), 'targetAttribute' => ['tipo_despesa_id' => 'tipo_despesa_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'despesa_id' => 'Despesa ID',
            'posto_id' => 'Posto ID',
            'tipo_despesa_id' => 'Categoria',
            'produto_negociacao_id' => 'Compra',
            'valor' => 'Valor #Total',
            'data_vencimento' => 'Data de Vencimento',
            'data_pagamento' => 'Data de Pagamento',
            'observacao' => 'Observações',
            'status' => 'Situações',
        ];
    }

    public function getPosto()
    {
        return $this->hasOne(Posto::className(), ['posto_id' => 'posto_id']);
    }

    public function getTipoDespesa()
    {
        return $this->hasOne(TipoDespesa::className(), ['tipo_despesa_id' => 'tipo_despesa_id']);
    }
}
