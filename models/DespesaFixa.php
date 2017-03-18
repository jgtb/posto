<?php

namespace app\models;

use Yii;

class DespesaFixa extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'despesa_fixa';
    }

    public function rules()
    {
        return [
            [['posto_id', 'tipo_despesa_id'], 'required'],
            [['posto_id', 'tipo_despesa_id', 'referencial', 'status'], 'integer'],
            [['valor'], 'number'],
            [['observacao'], 'string', 'max' => 500],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
            [['tipo_despesa_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDespesa::className(), 'targetAttribute' => ['tipo_despesa_id' => 'tipo_despesa_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'despesa_fixa_id' => 'Despesa Fixa ID',
            'posto_id' => 'Posto ID',
            'tipo_despesa_id' => 'Tipo Despesa ID',
            'referencial' => 'Referencial',
            'valor' => 'Valor',
            'observacao' => 'Observacao',
            'status' => 'Status',
        ];
    }

    public function getPosto()
    {
        return $this->hasOne(Posto::className(), ['posto_id' => 'posto_id']);
    }

    public function getTipoDespesa()
    {
        return $this->hasOne(TipoDespesa::className(), ['tipo_despesa_id' => 'tipo_despesa_id']);
    }
}
