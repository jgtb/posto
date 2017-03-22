<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\money\MaskMoney;

$this->title = 'Despesas Fixas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="despesa-fixa-index">

    <h1><?= Html::encode($this->title) ?> <?= $id == 1 ? '#' . Yii::$app->user->identity->getPosto() : '#Carro Tanque' ?></h1>

    <p>
        <?= Html::a('Nova Despesa Fixa', ['create', 'id' => $id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-despesa-fixa', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'url' => Yii::$app->homeUrl . Yii::$app->controller->id . '?id=' . $id]]); ?>

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
        'pager' => [
            'linkOptions' => ['data-pjax' => 0]
        ],
        'emptyText' => 'Nenhum resultado encontrado.',
        'columns' => [
            ['attribute' => 'tipo_despesa_id', 'value' => function ($model) {
                    return $model->tipoDespesa->descricao;
                }],
            ['attribute' => 'valor', 'format' => 'raw', 'filter' => MaskMoney::widget(['model' => $searchModel, 'attribute' => 'valor', 'pluginOptions' => ['prefix' => 'R$ ', 'allowNegative' => false, 'allowZero' => true, 'thousands' => '.', 'decimal' => ',',]]), 'value' => function ($model) {
                    return 'R$ ' . number_format($model->valor, 2, ',', '.');
                }],
            ['attribute' => 'observacao', 'value' => function ($model) {
                    return $model->observacao != NULL ? $model->observacao : 'Não inserido';
                }],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'contentOptions' => ['style' => 'width: 8%;'],
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
