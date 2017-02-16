<?php

use yii\helpers\Html;

$this->title = 'Alterar Posto: ' . $model->descricao;
$this->params['breadcrumbs'][] = ['label' => 'Meus Postos', 'url' => ['site/meus-postos']];
$this->params['breadcrumbs'][] = 'Alterar';
?>
<div class="posto-update">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel-meus-postos">
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

</div>
