<?php

namespace app\models;

use Yii;

class Relatorio extends \yii\db\ActiveRecord {

    public $data;

    public static function tableName() {
        return 'relatorio';
    }

    public function rules() {
        return [
            [['referencial', 'data'], 'required', 'message' => 'Campo Obrigatório'],
            [['referencial'], 'integer'],
            [['data_inicial', 'data_final'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'relatorio_id' => 'Relatorio ID',
            'referencial' => 'Relatório',
            'data_inicial' => 'Data Inicial',
            'data_final' => 'Data Final',
        ];
    }

    public function getRelatorioURL() {
        $relatoriosURL = ['total-compra', 'total-venda', 'total-despesa-posto', 'total-despesa-caminhao', 'total-caminhao', 'total-aluguel', 'total-geral'];
        return $relatoriosURL[$this->referencial];
    }

    public function getEstoqueInicial($tipoCombustivelID) {
        $estoqueVendaInicial = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['<', 'DATE(registro.data)', $this->data_inicial])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        $estoqueRetornoInicial = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['<', 'DATE(registro.data)', $this->data_inicial])
                ->sum('bico_registro.retorno');

        $estoqueCompraInicial = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $tipoCombustivelID, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['<', 'DATE(data)', $this->data_inicial])
                ->sum('qtde');

        $estoqueInicial = ($estoqueCompraInicial - $estoqueVendaInicial) + $estoqueRetornoInicial;

        return $estoqueInicial;
    }

    public function getEstoqueFinal($tipoCombustivelID) {
        $estoqueVendaFinal = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['<=', 'DATE(data)', $this->data_final])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        $estoqueRetornoFinal = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['<=', 'DATE(data)', $this->data_final])
                ->sum('bico_registro.retorno');

        $estoqueCompraFinal = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $tipoCombustivelID, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['<=', 'DATE(data)', $this->data_final])
                ->sum('qtde');

        $estoqueFinal = ($estoqueCompraFinal - $estoqueVendaFinal) + $estoqueRetornoFinal;

        return $estoqueFinal;
    }

    public function getEntrada($tipoCombustivelID) {
        $entrada = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => $tipoCombustivelID, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['>', 'DATE(data)', $this->data_inicial])
                ->andWhere(['<=', 'DATE(data)', $this->data_final])
                ->sum('qtde');

        return $entrada;
    }

    public function getSaida($tipoCombustivelID) {
        $saida = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['between', 'DATE(data)', $this->data_inicial, $this->data_final])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        return $saida;
    }

    public function getRetorno($tipoCombustivelID) {
        $retorno = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => $tipoCombustivelID, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['between', 'DATE(data)', $this->data_inicial, $this->data_final])
                ->sum('bico_registro.retorno');

        return $retorno;
    }

    public function getVendaGeral() {
        $modelsVendaGeral = Bico::find()
                ->leftJoin('bico_registro', 'bico.bico_id = bico_registro.bico_id')
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->where(['between', 'DATE(data)', $this->data_inicial, $this->data_final])
                ->andWhere(['registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->orderBy(['bico.descricao' => SORT_ASC])
                ->all();

        return $modelsVendaGeral;
    }

    public function getCompraGeral() {
        $modelsCompraGeral = ValorSaida::find()
                ->leftJoin('bico_registro', 'valor_saida.bico_registro_id = bico_registro.bico_registro_id')
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('produto_negociacao', 'valor_saida.produto_negociacao_id = produto_negociacao.produto_negociacao_id')
                ->where(['between', 'DATE(registro.data)', $this->data_inicial, $this->data_final])
                ->andWhere(['registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->groupBy(['produto_negociacao.valor'])
                ->all();
                        
        return $modelsCompraGeral;
    }

    public function getValorCombustivel($tipoCombustivelID) {
        if ($tipoCombustivelID == 1)
            return ValorCombustivel::findOne(['valor_combustivel_id' => Yii::$app->user->identity->posto_id])->valor_gasolina;
        if ($tipoCombustivelID == 2)
            return ValorCombustivel::findOne(['valor_combustivel_id' => Yii::$app->user->identity->posto_id])->valor_diesel;
    }

    public function getDespesaGeral() {
        $modelsDespesaGeral = TipoDespesa::find()
                ->leftJoin('despesa', 'tipo_despesa.tipo_despesa_id = despesa.tipo_despesa_id')
                ->where(['between', 'data_vencimento', $this->data_inicial, $this->data_final])
                ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id, 'referencial' => 1, 'despesa.status' => 1])
                ->all();

        return $modelsDespesaGeral;
    }

    public function getVendaOutrosGeralQ() {
        $outrosQTDE = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 1, 'status' => 2, 'produto_id' => 3, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['between', 'data', $this->data_inicial, $this->data_final])
                ->sum('qtde');

        return $outrosQTDE;
    }

    public function getVendaOutrosGeralT() {
        $outrosValor = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 1, 'status' => 2, 'produto_id' => 3, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['between', 'data', $this->data_inicial, $this->data_final])
                ->sum('qtde * valor');

        return $outrosValor;
    }

}
