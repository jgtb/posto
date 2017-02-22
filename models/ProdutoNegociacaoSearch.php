<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProdutoNegociacao;

class ProdutoNegociacaoSearch extends ProdutoNegociacao {

    public function rules() {
        return [
            [['produto_id', 'negociacao_id', 'valor', 'qtde', 'nota_fiscal', 'observacao', 'status', 'data'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params, $id) {
        $query = ProdutoNegociacao::find()
                ->joinWith('produto')
                ->where(['negociacao_id' => $id, 'posto_id' => Yii::$app->user->identity->posto_id, 'produto_negociacao.status' => [1, 2]])
                ->orderBy(['produto_negociacao.produto_negociacao_id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'produto_id' => ['asc' => ['produto.descricao' => SORT_ASC], 'desc' => ['produto.descricao' => SORT_DESC]],
                    'negociacao_id' => ['asc' => ['negociacao.descricao' => SORT_ASC], 'desc' => ['negociacao.descricao' => SORT_DESC]],
                    'valor',
                    'qtde',
                    'nota_fiscal',
                    'observacao',
                    'status',
                    'data',
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->data)
            $query->andFilterWhere(['=', 'data', date('Y-m-d', strtotime(str_replace('/', '-', $this->data)))]);

        $query->andFilterWhere(['like', 'produto.descricao', $this->produto_id])
                ->andFilterWhere(['like', 'produto_negociacao.valor', $this->valor])
                ->andFilterWhere(['like', 'produto_negociacao.qtde', $this->qtde])
                ->andFilterWhere(['like', 'produto_negociacao.observacao', $this->observacao])
                ->andFilterWhere(['like', 'produto_negociacao.status', $this->status])
                ->andFilterWhere(['like', 'produto_negociacao.nota_fiscal', $this->nota_fiscal]);

        return $dataProvider;
    }

}
