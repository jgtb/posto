<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Estoque';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-estoque">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <h1 class="text-center"><?= Html::encode($this->title) ?> #<?= Yii::$app->user->identity->getPosto() ?></h1>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="panel-body">

                    <?php Pjax::begin(['id' => 'pjax-produto', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]); ?>    

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        //'filterModel' => $searchModel,
                        'summary' => false,
                        'export' => false,
                        'pjax' => false,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => false,
                        'columns' => [
                            ['attribute' => 'produto_id', 'label' => 'Produto', 'value' => function ($model) {
                                    return $model->descricao;
                                }, 'group' => true],
                            ['attribute' => 'qtde', 'label' => 'Quantidade', 'value' => function ($model) {
                                    return $model->getQuantidade();
                                }, 'group' => true],
                        ],
                    ]);
                    ?>

                    <?php Pjax::end(); ?>

                </div>
            </div>
        </div>
    </div>


</div>
