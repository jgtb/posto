<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="despesa-fixa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tipo_despesa_id')->textInput() ?>

    <?= $form->field($model, 'valor')->textInput() ?>

    <?= $form->field($model, 'observacao')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
