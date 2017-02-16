<?php

use yii\helpers\Html;

$this->title = 'Alterar Registro: ' . $model->data;
$this->params['breadcrumbs'][] = ['label' => 'Registros', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Retorno #' . $model->data, 'url' => ['retorno', 'id' => $model->registro_id]];
$this->params['breadcrumbs'][] = 'Alterar';
?>
<div class="registro-update">

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
                        'modelsBicoRegistro' => $modelsBicoRegistro,
                        'estoque' => $model->getEstoque()
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>
