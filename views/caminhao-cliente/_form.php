<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\models\Cliente;
use app\models\TipoCombustivel;
use kartik\money\MaskMoney;
?>

<div class="caminhao-cliente-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cliente_id')->dropDownList(ArrayHelper::map(Cliente::findAll(['status' => 1]), 'cliente_id', 'nome'), ['prompt' => 'Selecione o Cliente', 'disabled' => !$model->isNewRecord ? true : false]) ?>

    <?= $form->field($model, 'tipo_combustivel_id')->dropDownList(ArrayHelper::map(TipoCombustivel::find()->all(), 'tipo_combustivel_id', 'descricao'), ['prompt' => 'Selecione o Combustível', 'disabled' => !$model->isNewRecord && in_array($model->cliente_id, [1, 2]) ? true : false]) ?>

    <?=
    $form->field($model, 'valor_litro')->widget(MaskMoney::classname(), [
        'pluginOptions' => [
            'prefix' => 'R$ ',
            'allowNegative' => false,
            'allowZero' => true,
            'thousands' => '.',
            'decimal' => ',',
        ]
    ]);
    ?>

    <?= $form->field($model, 'valor_carrada')->textInput() ?>

    <?=
    $form->field($model, 'valor_frete')->widget(MaskMoney::classname(), [
        'pluginOptions' => [
            'prefix' => 'R$ ',
            'allowNegative' => false,
            'allowZero' => true,
            'thousands' => '.',
            'decimal' => ',',
        ]
    ]);
    ?>

    <?= $form->field($model, 'nota_fiscal')->textInput() ?>

    <?=
    $form->field($model, 'data')->widget(DatePicker::className(), [
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

