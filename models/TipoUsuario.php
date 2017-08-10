<?php

namespace app\models;

use Yii;

class TipoUsuario extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'tipo_usuario';
    }

    public function rules()
    {
        return [
            [['descricao_singular', 'descricao_plural'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipo_usuario_id' => 'Tipo Usuario ID',
            'descricao_singular' => 'Descrição Singular',
            'descricao_plural' => 'Descrição Plural',
        ];
    }

    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['tipo_usuario_id' => 'tipo_usuario_id']);
    }
}
