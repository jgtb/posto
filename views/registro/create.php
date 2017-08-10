<?php

use yii\helpers\Html;

$this->title = 'Novo Registro';
$this->params['breadcrumbs'][] = ['label' => 'Registros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registro-create">

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
                        'estoque' => $estoque
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>
