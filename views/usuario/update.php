<?php

use yii\helpers\Html;

$this->title = 'Alterar ' . $model->tipoUsuario->descricao_singular . ': ' . $model->nome;
$this->params['breadcrumbs'][] = $model->nome;
?>
<div class="usuario-update">

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
