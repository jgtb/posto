<?php

namespace app\models;

use Yii;

class CaminhaoCliente extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'caminhao_cliente';
    }

    public function rules() {
        return [
            [['caminhao_id', 'cliente_id', 'tipo_combustivel_id', 'produto_negociacao_id', 'valor_litro', 'valor_carrada', 'valor_frete', 'nota_fiscal', 'data', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['caminhao_id', 'cliente_id', 'tipo_combustivel_id', 'valor_carrada', 'status'], 'integer'],
            [['valor_carrada'], 'checaEstoqueUpdateAluguel', 'on' => ['update']],
            [['valor_litro'], 'number'],
            [['data'], 'safe'],
            [['nota_fiscal', 'observacao'], 'string', 'max' => 500],
            [['caminhao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Caminhao::className(), 'targetAttribute' => ['caminhao_id' => 'caminhao_id']],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['cliente_id' => 'cliente_id']],
            [['tipo_combustivel_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCombustivel::className(), 'targetAttribute' => ['tipo_combustivel_id' => 'tipo_combustivel_id']],
        ];
    }

    public function checaEstoqueUpdateAluguel($attribute, $params) {
        if ($this->produto_negociacao_id != 0) {
            $valorSaida = ValorSaida::find()
                    ->where(['produto_negociacao_id' => $this->produto_negociacao_id])
                    ->sum('valor');

            $valorSaida = $valorSaida != NULL ? $valorSaida : 0;

            if ($this->valor_carrada < $valorSaida) {
                $this->addError($attribute, 'Saída de #' . $valorSaida . '. Limite de: ' . $valorSaida);
            }
        }

        return true;
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
