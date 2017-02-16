<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="cliente-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
