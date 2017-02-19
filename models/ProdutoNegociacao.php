<?php

namespace app\models;

use Yii;

class ProdutoNegociacao extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'produto_negociacao';
    }

    public function rules() {
        return [
            [['posto_id', 'produto_id', 'negociacao_id', 'valor', 'qtde', 'nota_fiscal', 'data'], 'required', 'on' => ['create', 'update'], 'message' => 'Campo obrigatório'],
            [['produto_id', 'negociacao_id', 'qtde', 'status'], 'integer'],
            //[['qtde'], 'checaEstoqueCreateVenda', 'on' => ['create']],
            //[['qtde'], 'checaEstoqueUpdateVenda', 'on' => ['update']],
            [['qtde'], 'checaEstoqueUpdateCompra', 'on' => ['update']],
            [['valor'], 'number'],
            [['nota_fiscal'], 'string', 'max' => 225],
            [['observacao'], 'string', 'max' => 500],
            [['data'], 'safe'],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
            [['negociacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Negociacao::className(), 'targetAttribute' => ['negociacao_id' => 'negociacao_id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::className(), 'targetAttribute' => ['produto_id' => 'produto_id']],
        ];
    }

    public function checaEstoqueCreateVenda($attribute, $params) {
        if ($this->negociacao_id == 1) {
            $qtdeCompra = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            $qtdeVenda = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 1, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            $result = $qtdeCompra - $qtdeVenda;

            if ($this->qtde > $result) {
                $this->addError($attribute, 'Estoque indisponível. Limite de: ' . $result);
            }
            return true;
        }

        return true;
    }

    public function checaEstoqueUpdateVenda($attribute, $params) {
        if ($this->negociacao_id == 1) {
            $qtdeCompra = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            $qtdeVenda = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 1, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            $result = ($qtdeCompra - $qtdeVenda) + ProdutoNegociacao::findOne(['produto_negociacao_id' => $this->produto_negociacao_id])->qtde;

            if ($this->qtde > $result) {
                $this->addError($attribute, 'Estoque indisponível. Limite de: ' . $result);
            }
            return true;
        }

        return true;
    }

    public function checaEstoqueUpdateCompra($attribute, $params) {
        if ($this->negociacao_id == 2) {
            $qtdeCompra = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            $qtdeVenda = ProdutoNegociacao::find()
                    ->where(['negociacao_id' => 1, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])
                    ->sum('qtde');

            if (in_array($this->produto_id, [1, 2])) {
                $qtdeVendaGD = BicoRegistro::find()
                        ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                        ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                        ->where(['bico.tipo_combustivel_id' => $this->produto_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                        ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

                $qtdeRetornoGD = BicoRegistro::find()
                        ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                        ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                        ->where(['bico.tipo_combustivel_id' => $this->produto_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                        ->sum('bico_registro.retorno');

                $estoqueGD = ($qtdeCompra - ($qtdeVendaGD - $qtdeRetornoGD));

                $limite = ProdutoNegociacao::findOne(['produto_negociacao_id' => $this->produto_negociacao_id])->qtde - $estoqueGD;

                if ($this->qtde < $limite) {
                    $this->addError($attribute, 'Estoque indisponível. Limite de: ' . $limite);
                }
            } else {
                $result = ProdutoNegociacao::findOne(['produto_negociacao_id' => $this->produto_negociacao_id])->qtde - ($qtdeCompra - $qtdeVenda);
                if ($this->qtde < $result) {
                    $this->addError($attribute, 'Estoque indisponível. Limite de: ' . $result);
                }
            }
            return true;
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

    public function getStatus() {
        return $this->status == 1 ? 'Não Pago' : 'Pago';
    }

}
