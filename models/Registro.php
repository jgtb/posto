<?php

namespace app\models;

use Yii;

class Registro extends \yii\db\ActiveRecord {

    public $registroID, $registroData, $registroDataSistema, $bicoRegistroBicoID, $bicoDescricao, $bicoRegistroValor, $bicoRegistroAtual, $bicoRegistroAnterior, $bicoRegistroRetorno, $bombaDescricao, $tipoCombustivelDescricao;

    public static function tableName() {
        return 'registro';
    }

    public function rules() {
        return [
            [['posto_id', 'data', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['posto_id', 'status'], 'integer'],
            [['data', 'data_sistema'], 'safe'],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
        ];
    }

    public function attributeLabels() {
        return [
            'registro_id' => 'Registro ID',
            'posto_id' => 'Posto ID',
            'data' => 'Data',
            'data_sistema' => 'Data Sistema',
            'status' => 'Situação',
        ];
    }

    public function getBicoRegistros() {
        return $this->hasMany(BicoRegistro::className(), ['registro_id' => 'registro_id']);
    }

    public function getPosto() {
        return $this->hasOne(Posto::className(), ['posto_id' => 'posto_id']);
    }

    public function getEstoque() {
        $qtdeCompraGasolina = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => 1, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('qtde');

        $qtdeVendaGasolina = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 1, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        $qtdeVendaGasolinaRetorno = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 1, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.retorno');

        $resultGasolina = ($qtdeCompraGasolina - $qtdeVendaGasolina) != NULL ? ($qtdeCompraGasolina - $qtdeVendaGasolina) + $qtdeVendaGasolinaRetorno : 0;

        $qtdeCompraDiesel = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => 2, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('qtde');

        $qtdeVendaDiesel = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 2, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        $qtdeVendaDieselRetorno = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 2, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.retorno');

        $resultDiesel = ($qtdeCompraDiesel - $qtdeVendaDiesel) != NULL ? ($qtdeCompraDiesel - $qtdeVendaDiesel) + $qtdeVendaDieselRetorno : 0;

        $result = ['Gasolina' => $resultGasolina, 'Diesel' => $resultDiesel];

        return $result;
    }

    public function deleteRegistros() {
        $modelsRegistro = Registro::find()
                ->where(['posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['>', 'registro_id', $this->registro_id])
                ->all();

        if ($modelsRegistro) {
            foreach ($modelsRegistro as $modelRegistro) {
                $modelsBicoRegistro = BicoRegistro::findAll(['registro_id' => $modelRegistro->registro_id]);
                if ($modelsBicoRegistro) {
                    foreach ($modelsBicoRegistro as $modelBicoRegistro) {
                        $modelBicoRegistro->delete();
                    }
                }
            }
        }
        Registro::findOne(['registro_id' => $this->registro_id])->delete();
    }

    public function deleteAllBicoRegistro() {
        $modelsBicoRegistro = BicoRegistro::findAll(['registro_id' => $this->registro_id]);

        foreach ($modelsBicoRegistro as $modelBicoRegistro) {
            $modelsValorSaida = ValorSaida::findAll(['bico_registro_id' => $modelBicoRegistro->bico_registro_id]);
            foreach ($modelsValorSaida as $modelValorSaida) {
                ValorSaida::findOne(['valor_saida_id' => $modelValorSaida->valor_saida_id])->delete();
            }
            BicoRegistro::findOne(['bico_registro_id' => $modelBicoRegistro->bico_registro_id])->delete();
        }
    }
}
