<?php

use yii\helpers\Html;

$this->title = 'Nova ' . substr($model->negociacao->descricao, 0, -1);
$this->params['breadcrumbs'][] = ['label' => $model->negociacao->descricao, 'url' => ['index', 'id' => $model->negociacao_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-negociacao-create">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="panel-body">
                    <?=
                    $this->render('_form', [
                        'model' => $model,
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>
