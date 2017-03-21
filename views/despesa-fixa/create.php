<?php

use yii\helpers\Html;

$this->title = 'Nova Despesa Fixa';
$this->params['breadcrumbs'][] = ['label' => 'Despesa Fixas', 'url' => ['index', 'id' => $model->referencial]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="despesa-fixa-create">

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
