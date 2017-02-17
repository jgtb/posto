<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\models\BicoRegistro;
?>

<div class="registro-form">

    <?php $form = ActiveForm::begin(['id' => 'form-registro']); ?>

    <div id="estoque-error" class="alert alert-danger text-center text-uppercase hidden">
        <div class="panel-title">Estoque Indisponível</div>
    </div>

    <?php if ($model->isNewRecord) : ?>
        <div class="alert alert-info text-center text-uppercase">
            <div class="panel-title">Estoque</div>
            <br>
            <div class="row">
                <div class="col-lg-2"></div>
                <?php foreach ($estoque as $combustivel => $es) : ?>
                    <div class="col-lg-4">
                        <?= $combustivel ?> <span id="valor-<?= $combustivel ?>">#<?= $es ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?=
    $form->field($model, 'data')->widget(DatePicker::className(), [
        'language' => 'pt-BR',
        'removeButton' => ['icon' => 'trash'],
        'pluginOptions' => [
            'todayHighlight' => true,
            'format' => 'dd/mm/yyyy',
            'autoclose' => true,
        ],
    ])
    ?>

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
                <div class="panel-body bico bico-<?= strtolower($modelBicoRegistro->bico->tipoCombustivel->descricao) ?>">               
                    <?=
                    $form->field($modelBicoRegistro, 'registro_anterior')->textInput([
                        'name' => 'BicoRegistro[' . $modelBicoRegistro->bico_id . '][registro_anterior]',
                        'class' => 'registro-anterior form-control',
                        'value' => $model->isNewRecord ? BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelBicoRegistro->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->orderBy(['registro.registro_id' => SORT_DESC])->one()->registro_atual : NULL,
                        'readonly' => !$model->isNewRecord || BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelBicoRegistro->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->orderBy(['registro.registro_id' => SORT_DESC])->one()->registro_atual != NULL ? true : false,
                            ]
                    );
                    ?>
                    <?=
                    $form->field($modelBicoRegistro, 'registro_atual')->textInput([
                        'name' => 'BicoRegistro[' . $modelBicoRegistro->bico_id . '][registro_atual]',
                        'class' => 'registro-atual form-control',
                        'disabled' => !$model->isNewRecord ? true : false,
                            ]
                    );
                    ?>
                    <?= $form->field($modelBicoRegistro, 'retorno')->hiddenInput(['name' => 'BicoRegistro[' . $modelBicoRegistro->bico_id . '][retorno]'])->label(false); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($model->isNewRecord) : ?>
        <div class="alert alert-warning text-uppercase text-center">
            Certifique-se de que todos os Bicos estão registrados
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::button($model->isNewRecord ? 'Salvar' : 'Alterar', ['id' => 'submitButton', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript" src="<?= Yii::$app->request->baseUrl . '/js/jquery.min.js' ?>"></script>
<script type="text/javascript">

    function checaEstoque()
    {
        var estoqueGasolina = <?= $estoque['Gasolina'] ?>;
        var estoqueDiesel = <?= $estoque['Diesel'] ?>;

        var totalGasolina = 0;
        var totalDiesel = 0;
        $('.bico').each(function () {
            var $registroAnterior = $(this).find('.registro-anterior');
            var valorRegistroAnterior = $registroAnterior.val();

            var $registroAtual = $(this).find('.registro-atual');
            var valorRegistroAtual = $registroAtual.val();

            var variacao = valorRegistroAtual - valorRegistroAnterior;

            $(this).hasClass('bico-gasolina') ? totalGasolina += variacao : totalDiesel += variacao;

        });

        return totalGasolina > estoqueGasolina || totalDiesel > estoqueDiesel ? false : true;
    }

    function errorEstoque()
    {
        $('#estoque-error').removeClass('hidden');
        window.scrollTo(0, 0);
    }

    function submitForm()
    {
        $("#form-registro").submit();
        $('#estoque-error').addClass('hidden');
    }

    $("#submitButton").on('click', function (e) {
        e.preventDefault();

        checaEstoque() ? submitForm() : errorEstoque();
    });

</script>