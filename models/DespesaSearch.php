<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Despesa;

class DespesaSearch extends Despesa {

    public function rules() {
        return [
            [['tipo_despesa_id', 'valor', 'data_vencimento', 'data_pagamento', 'observacao'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params, $id) {
        $query = Despesa::find()
                ->joinWith('tipoDespesa')
                ->orderBy(['despesa.data_vencimento' => SORT_DESC]);

        $id == 1 ? $query->where(['posto_id' => Yii::$app->user->identity->posto_id, 'referencial' => $id, 'despesa.status' => 1]) :
                        $query->where(['referencial' => $id, 'despesa.status' => 1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'tipo_despesa_id' => ['asc' => ['tipo_despesa.descricao' => SORT_ASC], 'desc' => ['tipo_despesa.descricao' => SORT_DESC]],
                    'valor',
                    'data_vencimento',
                    'data_pagamento',
                    'observacao',
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->data_vencimento)
            $query->andFilterWhere(['=', 'data_vencimento', date('Y-m-d', strtotime(str_replace('/', '-', $this->data_vencimento)))]);

        if ($this->data_pagamento)
            $query->andFilterWhere(['=', 'data_pagamento', date('Y-m-d', strtotime(str_replace('/', '-', $this->data_pagamento)))]);

        $query->andFilterWhere(['like', 'tipo_despesa.descricao', $this->tipo_despesa_id])
                ->andFilterWhere(['like', 'despesa.valor', $this->valor])
                ->andFilterWhere(['like', 'despesa.observacao', $this->observacao]);

        return $dataProvider;
    }

}
