<?php

namespace app\models;

use Yii;

class Backup extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'backup';
    }

    public function rules()
    {
        return [
            [['posto_id'], 'integer'],
            [['descricao'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'backup_id' => 'Backup ID',
            'posto_id' => 'Posto ID',
            'descricao' => 'Descricao',
        ];
    }
}
