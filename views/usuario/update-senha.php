<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Alterar Senha';
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->usuario_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="usuario-update-senha">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => true]); ?>

                    <?= $form->field($model, 'senha_antiga')->passwordInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'nova_senha')->passwordInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'confirma_senha')->passwordInput(['maxlength' => true]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Alterar Senha', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
