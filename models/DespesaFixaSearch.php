<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DespesaFixa;

class DespesaFixaSearch extends Despesa {

    public function rules() {
        return [
            [['tipo_despesa_id', 'valor', 'observacao'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params, $id) {
        $query = DespesaFixa::find()
                ->joinWith('tipoDespesa');

        $id == 1 ? $query->where(['posto_id' => Yii::$app->user->identity->posto_id, 'referencial' => $id, 'despesa_fixa.status' => 1]) :
                        $query->where(['referencial' => $id, 'despesa_fixa.status' => 1]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'tipo_despesa_id' => ['asc' => ['tipo_despesa.descricao' => SORT_ASC], 'desc' => ['tipo_despesa.descricao' => SORT_DESC]],
                    'valor',
                    'observacao',
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'tipo_despesa.descricao', $this->tipo_despesa_id])
                ->andFilterWhere(['like', 'valor', $this->valor])
                ->andFilterWhere(['like', 'observacao', $this->observacao]);

        return $dataProvider;
    }

}
