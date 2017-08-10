<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\TipoDespesa;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
?>

<div class="despesa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tipo_despesa_id')->dropDownList(ArrayHelper::map(TipoDespesa::findAll(['status' => 1]), 'tipo_despesa_id', 'descricao'), ['prompt' => 'Selecione a Categoria']) ?>

    <?=
    $form->field($model, 'valor')->widget(MaskMoney::classname(), [
        'pluginOptions' => [
            'prefix' => 'R$ ',
            'allowNegative' => false,
            'allowZero' => true,
            'thousands' => '.',
            'decimal' => ',',
        ]
    ]);
    ?>

    <?=
    $form->field($model, 'data_vencimento')->widget(DatePicker::className(), [
        'language' => 'pt-BR',
        'removeButton' => ['icon' => 'trash'],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'dd/mm/yyyy',
            'autoclose' => true,
        ],
    ])
    ?>

    <?=
    $form->field($model, 'data_pagamento')->widget(DatePicker::className(), [
        'language' => 'pt-BR',
        'removeButton' => ['icon' => 'trash'],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'dd/mm/yyyy',
            'autoclose' => true,
        ],
    ])
    ?>

    <?= $form->field($model, 'observacao')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
