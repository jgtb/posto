<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DespesaFixa;

class DespesaFixaSearch extends DespesaFixa
{

    public function rules()
    {
        return [
            [['despesa_fixa_id', 'posto_id', 'tipo_despesa_id', 'referencial', 'status'], 'integer'],
            [['valor'], 'number'],
            [['observacao'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = DespesaFixa::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'observacao', $this->observacao]);

        return $dataProvider;
    }
}
