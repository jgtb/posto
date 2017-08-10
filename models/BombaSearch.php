<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bomba;

class BombaSearch extends Bomba {

    public $bomba_id, $bico_id;

    public function rules() {
        return [
            [['bomba_id', 'bico_id'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = Bomba::find()
                ->select('bomba.bomba_id as bombaID, '
                        . 'bomba.descricao as bombaDescricao, '
                        . 'bico.bico_id as bicoID, '
                        . 'bico.descricao as bicoDescricao, '
                        . 'bico.tipo_combustivel_id as bicoTipoCombustivelID, '
                        . 'bico.status as bicoStatus')
                ->leftJoin('bico', 'bomba.bomba_id = bico.bomba_id and bico.status = 1')
                ->where(['bomba.posto_id' => Yii::$app->user->identity->posto_id])
                ->andWhere(['bomba.status' => 1])
                ->orderBy(['bomba.descricao' => SORT_ASC, 'bico.descricao' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'bomba_id' => ['asc' => ['bomba.descricao' => SORT_ASC], 'desc' => ['bomba.descricao' => SORT_DESC]],
                    'bico_id' => ['asc' => ['bico.descricao' => SORT_ASC], 'desc' => ['bico.descricao' => SORT_DESC]],
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'bomba.descricao', $this->bomba_id])
                ->andFilterWhere(['like', 'bico.descricao', $this->bico_id]);

        return $dataProvider;
    }

}
