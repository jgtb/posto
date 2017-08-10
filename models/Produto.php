<?php

namespace app\models;

use Yii;

class Produto extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'produto';
    }

    public function rules() {
        return [
            [['descricao', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['status'], 'integer'],
            [['descricao'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'produto_id' => 'Produto ID',
            'descricao' => 'Descrição',
            'status' => 'Situação',
        ];
    }

    public function getProdutoNegociacaos() {
        return $this->hasMany(ProdutoNegociacao::className(), ['produto_id' => 'produto_id']);
    }

    public function getQuantidade($postoID = NULL) {
        switch ($this->produto_id) {
            case 1:
                return $this->getQuantidadeGasolinaDiesel(1, $postoID);
                break;
            case 2:
                return $this->getQuantidadeGasolinaDiesel(2, $postoID);
                break;
            default:
                return $this->getQuantidadeOutro($postoID);
                break;
        }
    }

    public function getQuantidadeGasolinaDiesel($tipoCombustivelID, $postoID = NULL) {
        $qtdeCompra = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => $postoID == NULL ? Yii::$app->user->identity->posto_id : $postoID])
                ->sum('qtde');

        $qtdeVenda = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => $postoID == NULL ? Yii::$app->user->identity->posto_id : $postoID])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        $qtdeRetorno = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => $postoID == NULL ? Yii::$app->user->identity->posto_id : $postoID])
                ->sum('bico_registro.retorno');

        $result = ($qtdeCompra - ($qtdeVenda - $qtdeRetorno));

        return $result != NULL ? $result : 0;
    }

    public function getQuantidadeOutro($postoID = NULL) {
        $qtdeCompra = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => $postoID == NULL ? Yii::$app->user->identity->posto_id : $postoID])
                ->sum('qtde');

        $qtdeVenda = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 1, 'status' => 1, 'produto_id' => $this->produto_id, 'posto_id' => $postoID == NULL ? Yii::$app->user->identity->posto_id : $postoID])
                ->sum('qtde');

        $result = $qtdeCompra - $qtdeVenda;

        return $result != NULL ? $result : 0;
    }

}
