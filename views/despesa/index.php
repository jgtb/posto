<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;

$this->title = 'Despesas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="despesa-index">

    <h1><?= Html::encode($this->title) ?> <?= $id == 1 ? '#' . Yii::$app->user->identity->getPosto() : '#Carro Tanque' ?></h1>

    <p>
        <?= Html::a('Nova Despesa', ['create', 'id' => $id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-despesa', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'url' => Yii::$app->homeUrl . Yii::$app->controller->id . '?id=' . $id]]); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'export' => false,
        'pjax' => false,
        'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'hover' => false,
        'emptyText' => 'Nenhum resultado encontrado.',
        'columns' => [
            ['attribute' => 'tipo_despesa_id', 'value' => function ($model) {
                    return $model->tipoDespesa->descricao;
                }],
            ['attribute' => 'valor', 'format' => 'raw', 'filter' => MaskMoney::widget(['model' => $searchModel, 'attribute' => 'valor', 'pluginOptions' => ['prefix' => 'R$ ', 'allowNegative' => false, 'allowZero' => true, 'thousands' => '.', 'decimal' => ',',]]), 'value' => function ($model) {
                    return 'R$ ' . number_format($model->valor, 2, ',', '.');
                }],
            ['attribute' => 'data_vencimento', 'format' => 'raw', 'filter' => DatePicker::widget(['model' => $searchModel, 'attribute' => 'data_vencimento', 'language' => 'pt-BR', 'removeButton' => ['icon' => 'trash'], 'pluginOptions' => ['format' => 'dd/mm/yyyy', 'autoclose' => true]]), 'value' => function ($model) {
                    return date('d/m/Y', strtotime($model->data_vencimento));
                }],
            ['attribute' => 'data_pagamento', 'format' => 'raw', 'filter' => DatePicker::widget(['model' => $searchModel, 'attribute' => 'data_pagamento', 'language' => 'pt-BR', 'removeButton' => ['icon' => 'trash'], 'pluginOptions' => ['format' => 'dd/mm/yyyy', 'autoclose' => true]]), 'value' => function ($model) {
                    return $model->data_pagamento != NULL ? date('d/m/Y', strtotime($model->data_pagamento)) : 'Não inserido';
                }],
            ['attribute' => 'observacao', 'value' => function ($model) {
                    return $model->observacao != NULL ? $model->observacao : 'Não inserido';
                }],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'contentOptions' => ['style' => 'width: 8%;'],
                'visibleButtons' => [
                    'update' => function ($model) {
                        return $model->produto_negociacao_id == 0 ? true : false;
                    },
                    'delete' => function ($model) {
                        return $model->produto_negociacao_id == 0 ? true : false;
                    },
                ],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, ['class' => 'btn btn-xs btn-primary', 'data-pjax' => 0, 'title' => 'Alterar Despesa']);
                    },
                    'delete' => function ($url, $model, $key) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['class' => 'btn btn-xs btn-danger', 'title' => 'Excluír Despesa', 'data-pjax' => 0, 'data-confirm' => 'Você tem certeza que deseja excluír este item?', 'data-method' => 'post']);
                    }
                ]
            ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
