<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\TipoUsuario;

$this->title = TipoUsuario::findOne(['tipo_usuario_id' => $id])->descricao_plural;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuario-index">

    <h1><?= Html::encode($this->title) ?> #<?= Yii::$app->user->identity->getPosto() ?></h1>

    <p>
        <?= Html::a('Novo ' . TipoUsuario::findOne(['tipo_usuario_id' => $id])->descricao_singular, ['create', 'id' => $id], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?php Pjax::begin(['id' => 'pjax-usuario', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'url' => Yii::$app->homeUrl . Yii::$app->controller->id . '?id=' . $id]]); ?>    

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
            'nome',
            ['attribute' => 'email', 'label' => 'Login / E-mail', 'value' => function ($model) {
                    return $model->email;
                }],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'visibleButtons' => [
                    'delete' => function ($model, $url, $key) {
                        return Yii::$app->user->identity->status == 2;
                    }
                ],
                'buttons' => [
                    'update' => function ($url, $model, $key) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, ['class' => 'btn btn-xs btn-primary', 'data-pjax' => 0, 'title' => 'Alterar ' . TipoUsuario::findOne(['tipo_usuario_id' => $id])->descricao_singular]);
                    },
                    'delete' => function ($url, $model, $key) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['class' => 'btn btn-xs btn-danger', 'title' => 'Excluír ' . TipoUsuario::findOne(['tipo_usuario_id' => $id])->descricao_singular, 'data-pjax' => 0, 'data-confirm' => 'Você tem certeza que deseja excluír este item?', 'data-method' => 'post']);
                    }
                ]
            ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
