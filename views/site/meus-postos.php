<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Meus Postos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-meus-postos">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel-meus-postos">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                    </div>
                    <div class="panel-body">
                        <?php if (Yii::$app->user->identity->status == 2) : ?>
                            <?= Html::a('Novo Posto', ['/posto/create'], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 15px;']) ?>
                        <?php endif; ?>

                        <?php Pjax::begin(['id' => 'pjax-posto', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]); ?>    

                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProviderPostoUsuario,
                            'summary' => false,
                            'export' => false,
                            'pjax' => false,
                            'bordered' => true,
                            'striped' => true,
                            'condensed' => true,
                            'responsive' => true,
                            'hover' => false,
                            'columns' => [
                                ['attribute' => 'descricao', 'label' => 'Postos', 'value' => function ($model) {
                                        return $model->posto->descricao;
                                    }],
                                ['class' => 'yii\grid\ActionColumn',
                                    'template' => '{update}',
                                    'visibleButtons' => [
                                        'update' => function () {
                                            return Yii::$app->user->identity->status == 2;
                                        }
                                    ],
                                    'buttons' => [
                                        'update' => function ($url, $model, $key) {
                                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/posto/update', 'id' => $model->posto_id], ['class' => 'btn btn-xs btn-primary', 'title' => 'Alterar Posto']);
                                        }
                                    ]
                                ],
                            ],
                        ]);
                        ?>

                        <?php Pjax::end(); ?> 

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>