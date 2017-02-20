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
            //[['qtde'], 'checaEstoqueCreateVenda', 'on' => ['create']],
            //[['qtde'], 'checaEstoqueUpdateVenda', 'on' => ['update']],
            [['qtde'], 'checaEstoqueUpdateCompra', 'on' => ['update']],
            [['valor'], 'number', 'on' => ['create', 'update']],
            [['nota_fiscal'], 'string', 'max' => 225, 'on' => ['create', 'update']],
            [['observacao'], 'string', 'max' => 500, 'on' => ['create', 'update']],
            [['data'], 'safe', 'on' => ['create', 'update']],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
            [['negociacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Negociacao::className(), 'targetAttribute' => ['negociacao_id' => 'negociacao_id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::className(), 'targetAttribute' => ['produto_id' => 'produto_id']],
        ];
    }

    public function checaEstoqueUpdateCompra($attribute, $params) {
        if ($this->negociacao_id == 2) {
            $valorSaida = ValorSaida::find()
                    ->leftJoin('produto_negociacao', 'valor_saida.produto_negociacao_id = produto_negociacao.produto_negociacao_id')
                    ->where(['produto_negociacao.produto_id' => $this->produto_id, 'produto_negociacao_id' => $this->produto_negociacao_id])
                    ->sum('valor');

            $valorSaida = $valorSaida != NULL ? $valorSaida : 0;

            if ($this->qtde < $valorSaida) {
                $this->addError($attribute, 'Saída de #' . $valorSaida . '. Limite de: ' . $valorSaida);
            }
        }

        return true;
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

        if ($this->negociacao_id == 2) {
            $qtdeCompra = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            $qtdeVenda = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 1, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            $result = $qtdeCompra - $qtdeVenda;

            if ($this->qtde <= $result) {
                return true;
            } else {
                Yii::$app->session->setFlash('danger', ['body' => 'Não foi possível excluír a compra. Estoque indisponível']);
                return false;
            }
        } else {
            return true;
        }
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
