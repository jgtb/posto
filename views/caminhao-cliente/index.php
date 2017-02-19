<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;

$this->title = 'Alugueis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="caminhao-cliente-index">

    <h1><?= Html::encode($this->title) ?> #Carro Tanque</h1>

    <p>
        <?= Html::a('Novo Aluguel', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-caminhao-cliente', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]); ?>    

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
            ['attribute' => 'cliente_id', 'value' => function ($model) {
                    return $model->cliente->nome;
                }],
            ['attribute' => 'tipo_combustivel_id', 'value' => function ($model) {
                    return $model->tipoCombustivel->descricao;
                }],
            ['attribute' => 'valor_litro', 'format' => 'raw', 'filter' => MaskMoney::widget(['model' => $searchModel, 'attribute' => 'valor_litro', 'pluginOptions' => ['prefix' => 'R$ ', 'allowNegative' => false, 'allowZero' => true, 'thousands' => '.', 'decimal' => ',',]]), 'value' => function ($model) {
                    return 'R$ ' . number_format($model->valor_litro, 2, ',', '.');
                }],
            ['attribute' => 'valor_carrada', 'value' => function ($model) {
                    return $model->valor_carrada;
                }],
            ['attribute' => 'valor_frete', 'format' => 'raw', 'filter' => MaskMoney::widget(['model' => $searchModel, 'attribute' => 'valor_litro', 'pluginOptions' => ['prefix' => 'R$ ', 'allowNegative' => false, 'allowZero' => true, 'thousands' => '.', 'decimal' => ',',]]), 'value' => function ($model) {
                    return 'R$ ' . number_format($model->valor_frete, 2, ',', '.');
                }],
            ['attribute' => 'nota_fiscal', 'value' => function ($model) {
                    return $model->nota_fiscal;
                }],
            ['attribute' => 'data', 'format' => 'raw', 'contentOptions' => ['style' => 'width: 20%;'], 'filter' => DatePicker::widget(['model' => $searchModel, 'attribute' => 'data', 'language' => 'pt-BR', 'removeButton' => ['icon' => 'trash'], 'pluginOptions' => ['format' => 'dd/mm/yyyy', 'autoclose' => true]]), 'value' => function ($model) {
                    return date('d/m/Y', strtotime($model->data));
                }],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'contentOptions' => ['style' => 'width: 8%;'],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, ['class' => 'btn btn-xs btn-primary', 'title' => 'Alterar Aluguel']);
                    },
                    'delete' => function ($url, $model, $key) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['class' => 'btn btn-xs btn-danger', 'title' => 'Excluír Aluguel', 'data-pjax' => 0, 'data-confirm' => 'Você tem certeza que deseja excluír este item?', 'data-method' => 'post']);
                    }
                ]
            ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
