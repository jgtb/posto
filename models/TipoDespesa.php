<?php

namespace app\models;

use Yii;

class TipoDespesa extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'tipo_despesa';
    }

    public function rules()
    {
        return [
            [['descricao', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['status'], 'integer'],
            [['descricao'], 'string', 'max' => 255],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'tipo_despesa_id' => 'Tipo Despesa ID',
            'descricao' => 'Descrição',
            'status' => 'Situação',
        ];
    }
    
    public function getDespesas()
    {
        return $this->hasMany(Despesa::className(), ['tipo_despesa_id' => 'tipo_despesa_id']);
    }
}
