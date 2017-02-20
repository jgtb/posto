<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\date\DatePicker;

$this->title = 'Registros';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registro-index">

    <h1><?= Html::encode($this->title) ?> #<?= Yii::$app->user->identity->getPosto() ?></h1>

    <p>
        <?= Html::a('Novo Registro', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?php Pjax::begin(['id' => 'pjax-registro', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]); ?>    

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
            ['attribute' => 'registro_id', 'label' => 'Registros #Dia', 'format' => 'raw', 'filter' => DatePicker::widget(['model' => $searchModel, 'attribute' => 'registro_id', 'language' => 'pt-BR', 'removeButton' => ['icon' => 'trash'], 'pluginOptions' => ['format' => 'dd/mm/yyyy', 'autoclose' => true]]), 'value' => function ($model) {
                    $data_sistema = Yii::$app->user->identity->status == 2 ? 'Data de Registro: ' . date('d/m/Y', strtotime($model->registroDataSistema)) . ' ás ' . date('H:i', strtotime($model->registroDataSistema)) . '<br>' : '';
                    return date('d/m/Y', strtotime($model->registroData)) . '<br>' . $data_sistema . Html::a('<span class="glyphicon glyphicon-plus"></span>', ['registro/retorno', 'id' => $model->registroID], ['class' => 'btn btn-xs btn-success', 'title' => 'Adicionar Retorno']) . '<div class="button-separator"></div>' . Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id' => $model->registroID], ['class' => 'btn btn-xs btn-primary', 'title' => 'Alterar Registro']) . '<div class="button-separator"></div>' . Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/registro/delete', 'id' => $model->registroID], ['class' => 'btn btn-xs btn-danger', 'data-method' => 'post', 'data-pjax' => 0, 'data-confirm' => 'Você tem certeza que deseja excluír este item?', 'title' => 'Excluír Registro']);;
                }, 'group' => true],
            ['attribute' => 'bico_registro_id', 'label' => 'Vendas #Bomba / Bico', 'format' => 'raw', 'value' => function ($model) {
                    return $model->bombaDescricao != NULL ? $model->bombaDescricao . ' / ' . $model->bicoDescricao . ' - ' . $model->tipoCombustivelDescricao . '<br>' . 'Valor Litro #' . $model->tipoCombustivelDescricao . ': R$ ' . number_format($model->bicoRegistroValor, 2, ',', '.') . '<br>' . 'Registro Anterior: ' . number_format($model->bicoRegistroAnterior, 0, '.', '.') . '<br>' . 'Registro Atual: ' . number_format($model->bicoRegistroAtual, 0, '.', '.') . '<br>' . 'Retorno: ' . number_format($model->bicoRegistroRetorno, 0, '.', '.') . '<br>' . 'Quantidade #Litro: ' . number_format((($model->bicoRegistroAtual - $model->bicoRegistroAnterior) - $model->bicoRegistroRetorno), 0, '.', '.')  : 'Nenhum resultado encontrado.';
                }, 'group' => true],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
