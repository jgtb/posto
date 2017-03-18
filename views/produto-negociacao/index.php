<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\Negociacao;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;

$this->title = Negociacao::findOne($id)->descricao;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="produto-negociacao-index">

    <h1><?= Html::encode($this->title) ?> #<?= Yii::$app->user->identity->getPosto() ?></h1>

    <p>
        <?= Html::a('Nova ' . substr(Negociacao::findOne($id)->descricao, 0, -1), ['create', 'id' => $id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-produto-negociacao', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'url' => Yii::$app->homeUrl . Yii::$app->controller->id . '?id=' . $id]]); ?>    

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
            ['attribute' => 'produto_id', 'value' => function ($model) {
                    return $model->produto->descricao;
                }],
            ['attribute' => 'data', 'format' => 'raw', 'filter' => DatePicker::widget(['model' => $searchModel, 'attribute' => 'data', 'language' => 'pt-BR', 'removeButton' => ['icon' => 'trash'], 'pluginOptions' => ['format' => 'dd/mm/yyyy', 'autoclose' => true]]), 'value' => function ($model) {
                    return date('d/m/Y', strtotime($model->data));
                }],
            ['attribute' => 'valor', 'format' => 'raw', 'filter' => MaskMoney::widget(['model' => $searchModel, 'attribute' => 'valor', 'pluginOptions' => ['prefix' => 'R$ ', 'allowNegative' => false, 'allowZero' => true, 'thousands' => '.', 'decimal' => ',',]]), 'value' => function ($model) {
                    return 'R$ ' . number_format($model->valor, 4, ',', '.');
                }],
            ['attribute' => 'qtde', 'format' => 'raw', 'value' => function ($model) {
                    $valorSaida = $model->negociacao_id == 2 ? 'Saída #' . number_format($model->getSaida(), 0, '.', '.') : '';
                    return number_format($model->qtde, 0, '.', '.') . '<br>' . $valorSaida;
                }],
            ['attribute' => 'nota_fiscal', 'value' => function ($model) {
                    return $model->nota_fiscal;
                }],
            ['attribute' => 'observacao', 'value' => function ($model) {
                    return $model->observacao != NULL ? $model->observacao : 'Não inserido';
                }],
            ['attribute' => 'status', 'contentOptions' => ['style' => 'width: 10%;'], 'visible' => $id == 1, 'filter' => [2 => 'Pago', 1 => 'Não Pago'], 'value' => function ($model) {
                    return $model->getStatus();
                }],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'contentOptions' => ['style' => 'width: 10%;'],
                'buttons' => [
                    'update' => function ($url, $model, $key) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, ['class' => 'btn btn-xs btn-primary', 'data-pjax' => 0, 'title' => 'Alterar ' . substr(Negociacao::findOne($id)->descricao, 0, -1)]);
                    },
                    'delete' => function ($url, $model, $key) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['class' => 'btn btn-xs btn-danger', 'title' => 'Excluír ' . substr(Negociacao::findOne($id)->descricao, 0, -1), 'data-pjax' => 0, 'data-confirm' => 'Você tem certeza que deseja excluír este item?', 'data-method' => 'post']);
                    }
                ]
            ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
