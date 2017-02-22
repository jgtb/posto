<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CaminhaoCliente;

class CaminhaoClienteSearch extends CaminhaoCliente {

    public function rules() {
        return [
            [['caminhao_id', 'cliente_id', 'tipo_combustivel_id', 'valor_carrada', 'valor_litro', 'valor_frete', 'nota_fiscal', 'data', 'observacao', 'status'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = CaminhaoCliente::find()
                ->joinWith('caminhao')
                ->joinWith('cliente')
                ->joinWith('tipoCombustivel')
                ->where(['caminhao_cliente.status' => 1])
                ->orderBy(['caminhao_cliente.data' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'caminhao_id' => ['asc' => ['caminhao.descricao' => SORT_ASC], 'desc' => ['caminhao.descricao' => SORT_DESC]],
                    'cliente_id' => ['asc' => ['cliente.nome' => SORT_ASC], 'desc' => ['cliente.nome' => SORT_DESC]],
                    'tipo_combustivel_id' => ['asc' => ['tipo_combustivel.descricao' => SORT_ASC], 'desc' => ['tipo_combustivel.descricao' => SORT_DESC]],
                    'valor_carrada',
                    'valor_litro',
                    'valor_frete',
                    'nota_fiscal',
                    'data',
                    'observacao',
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        if ($this->data)
            $query->andFilterWhere(['=', 'data', date('Y-m-d', strtotime(str_replace('/', '-', $this->data)))]);

        $query->andFilterWhere(['like', 'caminhao.descricao', $this->caminhao_id])
                ->andFilterWhere(['like', 'cliente.nome', $this->cliente_id])
                ->andFilterWhere(['like', 'tipo_combustivel.descricao', $this->tipo_combustivel_id])
                ->andFilterWhere(['like', 'valor_carrada', $this->valor_carrada])
                ->andFilterWhere(['like', 'valor_litro', $this->valor_litro])
                ->andFilterWhere(['like', 'nota_fiscal', $this->nota_fiscal])
                ->andFilterWhere(['like', 'observacao', $this->observacao]);

        return $dataProvider;
    }

}
