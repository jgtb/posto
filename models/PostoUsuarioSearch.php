<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PostoUsuario;

class PostoUsuarioSearch extends PostoUsuario
{
    
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PostoUsuario::find()
                ->joinWith('posto')
                ->where(['usuario_id' => Yii::$app->user->identity->usuario_id])->orderBy(['posto.descricao' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
