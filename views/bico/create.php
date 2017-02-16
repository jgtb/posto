<?php

use yii\helpers\Html;

$this->title = 'Novo Bico';
$this->params['breadcrumbs'][] = ['label' => 'Bombas & Bicos', 'url' => ['bomba/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bico-create">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="panel-body">
                    <h3 style="margin: auto; margin-bottom: 15px;"><?= Html::encode($model->bomba->descricao) ?></h3>                    
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
