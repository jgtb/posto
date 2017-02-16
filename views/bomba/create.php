<?php

use yii\helpers\Html;

$this->title = 'Nova Bomba';
$this->params['breadcrumbs'][] = ['label' => 'Bombas & Bicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bomba-create">

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
