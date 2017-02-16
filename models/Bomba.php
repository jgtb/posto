<?php

namespace app\models;

use Yii;

class Bomba extends \yii\db\ActiveRecord {

    public $bombaID, $bombaDescricao, $bicoID, $bicoTipoCombustivelID, $bicoDescricao, $bicoStatus;

    public static function tableName() {
        return 'bomba';
    }

    public function rules() {
        return [
            [['posto_id', 'descricao', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['posto_id', 'status'], 'integer'],
            [['descricao'], 'string', 'max' => 255],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
        ];
    }

    public function attributeLabels() {
        return [
            'bomba_id' => 'Bomba ID',
            'posto_id' => 'Posto ID',
            'descricao' => 'Descrição',
            'status' => 'Situação',
        ];
    }

    public function getBicos() {
        return $this->hasMany(Bico::className(), ['bomba_id' => 'bomba_id']);
    }

    public function getPosto() {
        return $this->hasOne(Posto::className(), ['posto_id' => 'posto_id']);
    }

}
