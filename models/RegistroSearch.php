<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Registro;

class RegistroSearch extends Registro {

    public $bico_registro_id;

    public function rules() {
        return [
            [['registro_id', 'bico_registro_id'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = Registro::find()
                ->select('registro.registro_id as registroID, registro.data as registroData, registro.data_sistema as registroDataSistema, bico_registro.bico_id as bicoRegistroBicoID, bico.descricao as bicoDescricao, bico_registro.valor as bicoRegistroValor, bico_registro.registro_atual as bicoRegistroAtual, bico_registro.registro_anterior as bicoRegistroAnterior, bico_registro.retorno as bicoRegistroRetorno, bomba.descricao as bombaDescricao, tipo_combustivel.descricao as tipoCombustivelDescricao')
                ->leftJoin('bico_registro', 'registro.registro_id = bico_registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->leftJoin('bomba', 'bico.bomba_id = bomba.bomba_id')
                ->leftJoin('tipo_combustivel', 'bico.tipo_combustivel_id = tipo_combustivel.tipo_combustivel_id')
                ->where(['registro.posto_id' => Yii::$app->user->identity->posto_id, 'registro.status' => 1])
                ->orderBy(['registro.registro_id' => SORT_DESC, 'bico.descricao' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'registro_id' => ['asc' => ['registro.registro_id' => SORT_ASC], 'desc' => ['registro.registro_id' => SORT_DESC]],
                    'bico_registro_id' => ['asc' => ['bomba.descricao' => SORT_ASC], 'desc' => ['bomba.descricao' => SORT_DESC]],
                ]
            ],
            'pagination' => [
                'pageSize' => 40
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->registro_id)
            $query->andFilterWhere(['=', 'registro.data', date('Y-m-d', strtotime(str_replace('/', '-', $this->registro_id)))]);

        $query->andFilterWhere(['like', 'bomba.descricao', $this->bico_registro_id]);

        return $dataProvider;
    }

}
