<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\models\DespesaFixa;

$this->title = 'Gerar Despesas Fixas';
$this->params['breadcrumbs'][] = ['label' => 'Despesas', 'url' => ['index', 'id' => $model->referencial]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="despesa-form">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="panel-body">

                    <?php $modelsDespesaFixa = $model->referencial == 1 ? DespesaFixa::findAll(['posto_id' => Yii::$app->user->identity->posto_id, 'referencial' => $model->referencial, 'status' => 1]) : DespesaFixa::findAll(['referencial' => $model->referencial, 'status' => 1]) ?>

                    <div style="color: #3c763d; background: #dff0d8; border-left: 3px solid #d6e9c6; padding: 10px 20px; margin: 0 0 15px 0;">
                        <p>Despesas Fixas:</p>
                        <ul>
                            <?php foreach ($modelsDespesaFixa as $modelDespesaFixa) : ?>
                                <?php $modelDespesaFixaObservacao = $modelDespesaFixa->observacao != NULL ? ' - ' . $modelDespesaFixa->observacao : '' ?>
                                <li><?= $modelDespesaFixa->tipoDespesa->descricao . ' - R$ ' . number_format($modelDespesaFixa->valor, 2, ',', '.') . $modelDespesaFixaObservacao ?></li>
                            <?php endforeach; ?>
                            <?php if ($modelsDespesaFixa == NULL) : ?>
                                <li>Nenhum resultado encontrado.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php $form = ActiveForm::begin(); ?>

                    <?=
                    $form->field($model, 'data_vencimento')->widget(DatePicker::className(), [
                        'options' => ['disabled' => $modelsDespesaFixa == NULL ? true : false],
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
                        'options' => ['disabled' => $modelsDespesaFixa == NULL ? true : false],
                        'language' => 'pt-BR',
                        'removeButton' => ['icon' => 'trash'],
                        'pluginOptions' => [
                            'todayHighlight' => true,
                            'format' => 'dd/mm/yyyy',
                            'autoclose' => true,
                        ],
                    ])
                    ?>

                    <div class="form-group">
                        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success', 'disabled' => $modelsDespesaFixa == NULL ? true : false]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>

</div>
