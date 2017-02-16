<?php

namespace app\models;

use Yii;

class Bico extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return 'bico';
    }

    public function rules()
    {
        return [
            [['bomba_id', 'tipo_combustivel_id', 'descricao'], 'required', 'message' => 'Campo obrigatório'],
            [['bomba_id', 'tipo_combustivel_id', 'status'], 'integer'],
            [['descricao'], 'string', 'max' => 255],
            [['bomba_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bomba::className(), 'targetAttribute' => ['bomba_id' => 'bomba_id']],
            [['tipo_combustivel_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCombustivel::className(), 'targetAttribute' => ['tipo_combustivel_id' => 'tipo_combustivel_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'bico_id' => 'Bico ID',
            'bomba_id' => 'Bomba',
            'tipo_combustivel_id' => 'Combustível',
            'descricao' => 'Descrição',
            'status' => 'Situação',
        ];
    }

    public function getBomba()
    {
        return $this->hasOne(Bomba::className(), ['bomba_id' => 'bomba_id']);
    }

    public function getTipoCombustivel()
    {
        return $this->hasOne(TipoCombustivel::className(), ['tipo_combustivel_id' => 'tipo_combustivel_id']);
    }

    public function getBicoRegistros()
    {
        return $this->hasMany(BicoRegistro::className(), ['bico_id' => 'bico_id']);
    }
}
