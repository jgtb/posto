<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Usuario;

class UsuarioSearch extends Usuario {

    public function rules() {
        return [
            [['nome', 'email'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params, $tipoUsuarioID) {
        $query = Usuario::find()
                ->leftJoin('posto_usuario', 'usuario.usuario_id = posto_usuario.usuario_id')
                ->where([
            'posto_usuario.posto_id' => Yii::$app->user->identity->posto_id,
            'tipo_usuario_id' => $tipoUsuarioID,
            'status' => 1
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'nome', $this->nome])
                ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }

}
