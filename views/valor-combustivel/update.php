<?php

use yii\helpers\Html;

$this->title = 'Alterar Preço de Venda #Gasolina & Diesel';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="valor_combustivel-update">

    <h1 class="text-center">Manutenção <br>#<?= Yii::$app->user->identity->getPosto() ?></h1>

    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
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
