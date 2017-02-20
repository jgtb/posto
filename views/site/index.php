<?php

use yii\helpers\Html;
use app\models\EstoqueSearch;
use app\models\ClienteSearch;
use kartik\grid\GridView;

$this->title = Yii::$app->user->identity->posto_id != 0 ? Yii::$app->user->identity->getPosto() : 'Carro Tanque';
?>
<div class="site-index">

    <?php if (Yii::$app->user->identity->posto_id != 0) : ?>
        <div class="text-center text-uppercase">
            <h3>Você está gerenciando Posto #<?= Yii::$app->user->identity->getPosto() ?></h3>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->user->identity->posto_id == 0) : ?>
        <div class="text-center text-uppercase">
            <h3>Você está gerenciando #Carro Tanque</h3>
        </div>
    <?php endif; ?>

    <br>

    <?php if (Yii::$app->user->identity->status == 2) : ?>
        <div class="row">
            <?php foreach ($modelsPosto as $index => $modelPosto) : ?>
                <div class="col-lg-4">
                    <div class="panel panel-default panel-no-margin">
                        <div class="panel-heading text-center text-uppercase">
                            <?= $modelPosto->descricao ?>
                        </div>
                        <div class="panel-body">
                            <div class="text-center">
                                <?= Html::a(Html::img($index % 2 ? Yii::$app->homeUrl . 'img/1.jpeg' : Yii::$app->homeUrl . 'img/2.jpeg', ['class' => 'img-thumbnail', 'style' => 'width: 43%; margin-bottom: 15px;']), ['troca-posto', 'id' => $modelPosto->posto_id], ['class' => 'alert-btn']) ?>
                            </div>
                            <div class="panel panel-default panel-no-margin">
                                <div class="panel-heading text-center">
                                    Estoque Atual
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $searchModel = new EstoqueSearch();
                                    $dataProvider = $searchModel->search(Yii::$app->request->post());
                                    ?>
                                    <?=
                                    GridView::widget([
                                        'dataProvider' => $dataProvider,
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
                                            ['attribute' => 'qtde', 'label' => 'Quantidade', 'value' => function ($model) use ($modelPosto) {
                                                    return number_format($model->getQuantidade($modelPosto->posto_id), 0, '.', '.');
                                                }, 'group' => true],
                                        ],
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-lg-4">
                <div class="panel panel-default panel-no-margin">
                    <div class="panel-heading text-center text-uppercase">
                        Carro Tanque
                    </div>
                    <div class="panel-body text-center">
                        <?= Html::a(Html::img(Yii::$app->homeUrl . 'img/3.jpeg', ['class' => 'img-thumbnail', 'style' => 'width: 320px; height: 140px; margin-bottom: 15px;']), ['troca-posto', 'id' => 0], ['class' => 'alert-btn']) ?>
                        <div class="panel panel-default panel-no-margin">
                            <div class="panel-heading text-center">
                                Clientes
                            </div>
                            <div class="panel-body">
                                <?php
                                $searchModel = new ClienteSearch();
                                $dataProvider = $searchModel->search(Yii::$app->request->post());
                                ?>
                                <?=
                                GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'summary' => false,
                                    'export' => false,
                                    'pjax' => false,
                                    'bordered' => true,
                                    'striped' => true,
                                    'condensed' => true,
                                    'responsive' => true,
                                    'hover' => false,
                                    'tableOptions' => ['class' => 'no-thead'],
                                    'columns' => [
                                        ['attribute' => 'nome', 'label' => 'Clientes', 'value' => function ($model) {
                                                return $model->nome;
                                            }, 'group' => true],
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

