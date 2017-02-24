<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;

$this->title = 'Relatórios';
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->user->identity->posto_id != 0 ?
                $relatorios = [0 => 'Compra', 1 => 'Venda', 2 => 'Despesa', 6 => 'Fechamento'] :
                $relatorios = [5 => 'Aluguel', 3 => 'Despesa', 4 => 'Fechamento'];
?>
<div class="relatorio-index">

    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
            <h1 class="text-center"><?= $this->title ?> <br> #<?= Yii::$app->user->identity->posto_id != 0 ? Yii::$app->user->identity->getPosto() : 'Carro Tanque' ?></h1>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="panel-body">                        
                    <?php $form = ActiveForm::begin(['options' => ['target' => '_blank']]); ?>

                    <?= $form->field($model, 'referencial')->dropDownList($relatorios, ['prompt' => 'Selecione um Relatório']) ?>

                    <?=
                    $form->field($model, 'data')->widget(DatePicker::classname(), [
                        'language' => 'pt-BR',
                        'attribute' => 'data_inicial',
                        'attribute2' => 'data_final',
                        'options' => ['placeholder' => 'Inicial'],
                        'options2' => ['placeholder' => 'Final'],
                        'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                        'type' => DatePicker::TYPE_RANGE,
                        'removeButton' => ['icon' => 'trash'],
                        'pluginOptions' => [
                            'todayHighlight' => true,
                            'format' => 'dd/mm/yyyy',
                            'autoclose' => true,
                        ],
                    ])->label('Data Inicial e Final');
                    ?>

                    <div class="form-group">
                        <?= Html::submitButton('Emitir', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>