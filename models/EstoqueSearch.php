<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Produto;

class EstoqueSearch extends Produto {

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = Produto::find()->where(['produto_id' => [1, 2]]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}
