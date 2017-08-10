<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Esqueceu a senha?';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-redefine">

    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
            <div class="panel-login">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                    </div>
                    <div class="panel-body">
                        <?php $form = ActiveForm::begin(); ?>

                        <?= $form->field($model, 'email')->textInput() ?>

                        <div class="form-group">
                            <?= Html::submitButton('Enviar', ['class' => 'btn btn-primary']) ?>
                            <hr>
                            <?= Html::a('Login', ['login']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>