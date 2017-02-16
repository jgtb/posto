<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Produto;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
?>

<div class="produto-negociacao-form">

    <?php $form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => true]); ?>

    <?= $form->field($model, 'produto_id')->dropDownList(ArrayHelper::map(Produto::findAll($model->negociacao_id == 1 ? ['produto_id' => 3] : ['produto_id' => [1, 2]]), 'produto_id', 'descricao'), ['prompt' => $model->negociacao_id == 2 ? 'Selecione o Produto' : ''])->label($model->negociacao_id == 2 ? 'Produto' : 'Outras Receitas') ?>

    <!-- ['options' => ['class' => in_array($model->produto_id, [1, 2]) ? 'hidden' : '']] -->
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

    <?= $form->field($model, 'observacao')->textarea(['rows' => 6]) ?>

    <?php if ($model->negociacao_id == 1) : ?>
        <?= $form->field($model, 'status')->dropDownList([2 => 'Pago', 1 => 'Não Pago']) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript" src="<?= Yii::$app->request->baseUrl . '/js/jquery.min.js' ?>"></script>
<script type="text/javascript">
    $(function () {
        /*
         $('#produtonegociacao-produto_id').on('change', function () {
         
         var produto_id = $(this).val();
         
         if (produto_id == 1 || produto_id == 2)
         {
         $('.field-produtonegociacao-valor').addClass('hidden');
         } else {
         $('.field-produtonegociacao-valor').removeClass('hidden');
         }
         
         });
         */
    });
</script>
