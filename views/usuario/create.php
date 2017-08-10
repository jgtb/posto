<?php

use yii\helpers\Html;

$this->title = 'Novo ' . $model->tipoUsuario->descricao_singular;
$this->params['breadcrumbs'][] = ['label' => $model->tipoUsuario->descricao_plural, 'url' => ['index', 'id' => $model->tipo_usuario_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuario-create">

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
