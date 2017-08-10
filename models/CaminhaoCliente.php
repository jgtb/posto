<?php

namespace app\models;

use Yii;

class CaminhaoCliente extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'caminhao_cliente';
    }

    public function rules() {
        return [
            [['caminhao_id', 'cliente_id', 'tipo_combustivel_id', 'valor_litro', 'valor_carrada', 'valor_frete', 'nota_fiscal', 'data', 'status'], 'required', 'on' => ['create', 'update'], 'message' => 'Campo obrigatório'],
            [['caminhao_id', 'cliente_id', 'tipo_combustivel_id', 'produto_negociacao_id', 'valor_carrada', 'status'], 'integer', 'on' => ['create', 'update']],
            [['valor_carrada'], 'compare', 'compareValue' => (double) ValorSaida::find()->where(['produto_negociacao_id' => $this->produto_negociacao_id])->sum('valor'), 'operator' => '>=', 'on' => ['update'], 'message' => 'Limite de #' . (double) ValorSaida::find()->where(['produto_negociacao_id' => $this->produto_negociacao_id])->sum('valor') . ''],
            [['valor_litro'], 'number', 'on' => ['create', 'update']],
            [['data'], 'safe', 'on' => ['create', 'update']],
            [['nota_fiscal', 'observacao'], 'string', 'max' => 500, 'on' => ['create', 'update']],
            [['caminhao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Caminhao::className(), 'targetAttribute' => ['caminhao_id' => 'caminhao_id']],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['cliente_id' => 'cliente_id']],
            [['tipo_combustivel_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCombustivel::className(), 'targetAttribute' => ['tipo_combustivel_id' => 'tipo_combustivel_id']],
        ];
    }
    
    public function attributeLabels() {
        return [
            'caminhao_cliente_id' => 'Caminhao Cliente ID',
            'caminhao_id' => 'Caminhão',
            'cliente_id' => 'Cliente',
            'tipo_combustivel_id' => 'Combustível',
            'produto_negociacao_id' => 'Compra',
            'valor_litro' => 'Valor #Litro',
            'valor_carrada' => 'Quantidade #Litro',
            'valor_frete' => 'Valor #Frete',
            'nota_fiscal' => 'Nota Fiscal',
            'data' => 'Data',
            'observacao' => 'Observações',
            'status' => 'Situação',
        ];
    }

    public function getCaminhao() {
        return $this->hasOne(Caminhao::className(), ['caminhao_id' => 'caminhao_id']);
    }

    public function getCliente() {
        return $this->hasOne(Cliente::className(), ['cliente_id' => 'cliente_id']);
    }

    public function getTipoCombustivel() {
        return $this->hasOne(TipoCombustivel::className(), ['tipo_combustivel_id' => 'tipo_combustivel_id']);
    }

}
