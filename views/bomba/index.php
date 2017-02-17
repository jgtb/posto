<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\TipoCombustivel;

$this->title = 'Bombas & Bicos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bomba-index">

    <h1><?= Html::encode($this->title) ?> #<?= Yii::$app->user->identity->getPosto() ?></h1>

    <p>
        <?= Html::a('Nova Bomba', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-bomba', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]); ?>    

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
            ['attribute' => 'bomba_id', 'format' => 'raw', 'label' => 'Bombas', 'value' => function ($model) {
                    return $model->bombaDescricao . '<br>' . '<span class="hidden">' . $model->bombaID . '</span>' . Html::a('<span class="glyphicon glyphicon-plus"></span>', ['/bico/create', 'id' => $model->bombaID], ['class' => 'btn btn-xs btn-success', 'title' => 'Novo Bico']) . '<div class="button-separator"></div>' . Html::a('<span class="glyphicon glyphicon-edit"></span>', ['/bomba/update', 'id' => $model->bombaID], ['class' => 'btn btn-xs btn-primary', 'title' => 'Alterar Bomba']) . '<div class="button-separator"></div>' . Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/bomba/delete', 'id' => $model->bombaID], ['class' => 'btn btn-xs btn-danger', 'data-method' => 'post', 'data-pjax' => 0, 'data-confirm' => 'Você tem certeza que deseja excluír este item?', 'title' => 'Excluír Bomba']);
                }, 'group' => true],
            ['attribute' => 'bico_id', 'format' => 'raw', 'label' => 'Bicos', 'value' => function ($model) {
                    return $model->bicoID != NULL ? '<span class="hidden">' . $model->bicoID . '</span>' . $model->bicoDescricao . '<br>' . TipoCombustivel::findOne($model->bicoTipoCombustivelID)->descricao . '<br>' . Html::a('<span class="glyphicon glyphicon-edit"></span>', ['/bico/update', 'id' => $model->bicoID], ['class' => 'btn btn-xs btn-primary', 'title' => 'Alterar Bico']) . '<div class="button-separator hidden"></div>' . Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/bico/delete', 'id' => $model->bicoID], ['class' => 'btn btn-xs btn-danger hidden', 'data-method' => 'post', 'data-pjax' => 0, 'data-confirm' => 'Você tem certeza que deseja excluír este item?', 'title' => 'Excluír Bico']) : 'Nenhum resultado encontrado.';
                }, 'group' => true],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
