<?php

namespace app\models;

use Yii;

class PostoUsuario extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'posto_usuario';
    }

    public function rules()
    {
        return [
            [['posto_id', 'usuario_id'], 'required'],
            [['posto_id', 'usuario_id'], 'integer'],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['usuario_id' => 'usuario_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'posto_usuario_id' => 'Posto Usuario ID',
            'posto_id' => 'Posto ID',
            'usuario_id' => 'Usuario ID',
        ];
    }

    public function getPosto()
    {
        return $this->hasOne(Posto::className(), ['posto_id' => 'posto_id']);
    }

    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['usuario_id' => 'usuario_id']);
    }
}
