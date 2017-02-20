<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Retorno #' . $model->data;
$this->params['breadcrumbs'][] = ['label' => 'Registros', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Registro #' . $model->data, 'url' => ['update', 'id' => $model->registro_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registro-retorno">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'data')->hiddenInput()->label(false) ?>

                    <?php if ($modelsBicoRegistro) : ?>
                        <?php foreach ($modelsBicoRegistro as $modelBicoRegistro) : ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-lg-7">
                                            <h3 class="panel-title"><?= $modelBicoRegistro->bico->bomba->descricao ?> / <?= $modelBicoRegistro->bico->descricao ?> - <?= $modelBicoRegistro->bico->tipoCombustivel->descricao ?></h3>
                                        </div>
                                        <div class="col-lg-5">
                                            <h4 class="panel-title">Valor do Litro R$ <?= number_format(Yii::$app->user->identity->getValorCombustivel($modelBicoRegistro->bico->tipo_combustivel_id), 2, ',', '.') ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?= $form->field($modelBicoRegistro, 'retorno')->textInput(['name' => 'BicoRegistro[' . $modelBicoRegistro->bico_id . '][retorno]']); ?>
                                </div>
                                <?= $form->field($modelBicoRegistro, 'valor')->hiddenInput(['name' => 'BicoRegistro[' . $modelBicoRegistro->bico_id . '][valor]'])->label(false); ?>
                                <?= $form->field($modelBicoRegistro, 'registro_anterior')->hiddenInput(['name' => 'BicoRegistro[' . $modelBicoRegistro->bico_id . '][registro_anterior]'])->label(false); ?>
                                <?= $form->field($modelBicoRegistro, 'registro_atual')->hiddenInput(['name' => 'BicoRegistro[' . $modelBicoRegistro->bico_id . '][registro_atual]'])->label(false); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Alterar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
