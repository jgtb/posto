<?php

namespace app\models;

use Yii;

class ProdutoNegociacao extends \yii\db\ActiveRecord {

    public $valor_frete;

    public static function tableName() {
        return 'produto_negociacao';
    }

    public function rules() {
        return [
            [['posto_id', 'produto_id', 'negociacao_id', 'valor', 'valor_frete', 'qtde', 'nota_fiscal', 'data'], 'required', 'on' => ['create', 'update'], 'message' => 'Campo obrigatório'],
            [['produto_id', 'negociacao_id', 'qtde', 'status'], 'integer'],
            [['qtde'], 'compare', 'compareValue' => (double) ValorSaida::find()->where(['produto_negociacao_id' => $this->produto_negociacao_id])->sum('valor'), 'operator' => '>=', 'on' => ['update'], 'message' => 'Limite de #' . (double) ValorSaida::find()->where(['produto_negociacao_id' => $this->produto_negociacao_id])->sum('valor') . ''],
            [['valor'], 'number', 'on' => ['create', 'update']],
            [['nota_fiscal'], 'string', 'max' => 225, 'on' => ['create', 'update']],
            [['observacao'], 'string', 'max' => 500, 'on' => ['create', 'update']],
            [['data'], 'safe', 'on' => ['create', 'update']],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
            [['negociacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Negociacao::className(), 'targetAttribute' => ['negociacao_id' => 'negociacao_id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::className(), 'targetAttribute' => ['produto_id' => 'produto_id']],
        ];
    }

    public function attributeLabels() {
        return [
            'produto_negociacao_id' => 'Produto Negociacao ID',
            'produto_id' => 'Produto',
            'negociacao_id' => 'Negociacao ID',
            'valor' => 'Valor Litro #Unidade',
            'qtde' => 'Quantidade',
            'nota_fiscal' => 'Nota Fiscal',
            'data' => 'Data',
            'observacao' => 'Observações',
            'status' => 'Situação',
        ];
    }

    public function getNegociacao() {
        return $this->hasOne(Negociacao::className(), ['negociacao_id' => 'negociacao_id']);
    }

    public function getProduto() {
        return $this->hasOne(Produto::className(), ['produto_id' => 'produto_id']);
    }

    public function checaEstoqueDeleteCompra() {
        $valorSaida = ValorSaida::find()
                ->leftJoin('produto_negociacao', 'valor_saida.produto_negociacao_id = produto_negociacao.produto_negociacao_id')
                ->where(['produto_negociacao.produto_id' => $this->produto_id, 'produto_negociacao.produto_negociacao_id' => $this->produto_negociacao_id])
                ->sum('valor_saida.valor');

        $valorSaida = $valorSaida != NULL ? $valorSaida : 0;
        return $valorSaida == 0 ? true : false;
    }

    public function getSaida() {
        $valorSaida = ValorSaida::find()
                ->leftJoin('produto_negociacao', 'valor_saida.produto_negociacao_id = produto_negociacao.produto_negociacao_id')
                ->where(['produto_negociacao.produto_id' => $this->produto_id, 'produto_negociacao.produto_negociacao_id' => $this->produto_negociacao_id])
                ->sum('valor_saida.valor');

        $valorSaida = $valorSaida != NULL ? $valorSaida : 0;

        return $valorSaida;
    }

    public function getStatus() {
        return $this->status == 1 ? 'Não Pago' : 'Pago';
    }

}
