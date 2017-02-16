<?php

namespace app\models;

use Yii;

class BicoRegistro extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'bico_registro';
    }

    public function rules() {
        return [
            [['registro_id', 'bico_id', 'registro_anterior', 'registro_atual', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['registro_atual'], 'compare', 'compareAttribute' => 'registro_anterior', 'operator' => '>=', 'type' => 'number', 'message' => 'Registro Atual deve ser maior que o Registro Anterior'],
            [['retorno'], 'compare', 'compareValue' => ($this->registro_atual - $this->registro_anterior), 'operator' => '<=', 'type' => 'number', 'message' => 'Retorno limite de: ' . ($this->registro_atual - $this->registro_anterior) . ''],
            [['registro_id', 'bico_id', 'status'], 'integer'],
            [['valor', 'registro_anterior', 'registro_atual', 'retorno'], 'number'],
            [['bico_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bico::className(), 'targetAttribute' => ['bico_id' => 'bico_id']],
            [['registro_id'], 'exist', 'skipOnError' => true, 'targetClass' => Registro::className(), 'targetAttribute' => ['registro_id' => 'registro_id']],
        ];
    }

    public function formName() {
        return 'bico-registro' . '-' . $this->bico_id;
    }

    public function attributeLabels() {
        return [
            'bico_registro_id' => 'Bico Registro ID',
            'registro_id' => 'Registro',
            'bico_id' => 'Bico',
            'valor' => 'Valor',
            'registro_anterior' => 'Registro Anterior',
            'registro_atual' => 'Registro Atual',
            'retorno' => 'Retorno',
            'status' => 'Situação',
        ];
    }

    public function getBico() {
        return $this->hasOne(Bico::className(), ['bico_id' => 'bico_id']);
    }

    public function getRegistro() {
        return $this->hasOne(Registro::className(), ['registro_id' => 'registro_id']);
    }

}
