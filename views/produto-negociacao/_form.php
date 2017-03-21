<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Produto;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
?>

<div class="produto-negociacao-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'produto_id')->dropDownList(ArrayHelper::map(Produto::findAll($model->negociacao_id == 1 ? ['produto_id' => 3] : ['produto_id' => [1, 2]]), 'produto_id', 'descricao'), ['prompt' => $model->negociacao_id == 2 ? 'Selecione o Produto' : '', 'disabled' => !$model->isNewRecord ? true : false])->label($model->negociacao_id == 2 ? 'Produto' : 'Outras Receitas') ?>

    <?=
    $form->field($model, 'valor')->widget(MaskMoney::classname(), [
        'pluginOptions' => [
            'precision' => 4,
            'prefix' => 'R$ ',
            'allowNegative' => false,
            'allowZero' => true,
            'thousands' => '.',
            'decimal' => ',',
        ]
    ]);
    ?>

    <?= $form->field($model, 'qtde')->textInput() ?>

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

    <?php if ($model->negociacao_id == 2) : ?>
        <?=
        $form->field($model, 'valor_frete')->widget(MaskMoney::classname(), [
            'pluginOptions' => [
                'prefix' => 'R$ ',
                'allowNegative' => false,
                'allowZero' => true,
                'thousands' => '.',
                'decimal' => ',',
            ]
        ])->label('Valor #Frete');
        ?>
    <?php endif; ?>

    <?= $form->field($model, 'observacao')->textarea(['rows' => 6]) ?>

    <?php if ($model->negociacao_id == 1) : ?>
        <?= $form->field($model, 'status')->dropDownList([2 => 'Pago', 1 => 'Não Pago']) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

