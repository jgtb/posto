<?php

use yii\helpers\Html;

$this->title = 'Alterar Despesa Fixa: ' . $model->tipoDespesa->descricao;
$this->params['breadcrumbs'][] = ['label' => 'Despesa Fixas', 'url' => ['index', 'id' => $model->referencial]];
$this->params['breadcrumbs'][] = 'Alterar';
?>
<div class="despesa-fixa-update">

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
