<?php

use app\models\Produto;
use app\models\BicoRegistro;
use app\models\ProdutoNegociacao;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Vendas');

$mpdf->WriteHTML('<h2 class="text-center">Relatório #Vendas</h2>');

$mpdf->WriteHTML('<h3 class="text-center">Posto #' . Yii::$app->user->identity->getPosto() . '</h3>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');

$modelsProduto = Produto::find()
        ->leftJoin('produto_negociacao', 'produto.produto_id = produto_negociacao.produto_id')
        ->where(['between', 'produto_negociacao.data', $model->data_inicial, $model->data_final])
        ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id])
        ->andWhere(['negociacao_id' => 1])
        ->andWhere(['produto_negociacao.status' => 2])
        ->all();

$modelsProdutoNegociacao = ProdutoNegociacao::find()
        ->where(['between', 'data', $model->data_inicial, $model->data_final])
        ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id])
        ->andWhere(['negociacao_id' => 1])
        ->andWhere(['produto_negociacao.status' => 2])
        ->all();

$modelsGasolina = BicoRegistro::find()
        ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
        ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
        ->where(['between', 'registro.data', $model->data_inicial, $model->data_final])
        ->andWhere(['bico.tipo_combustivel_id' => 1])
        ->andWhere(['registro.posto_id' => Yii::$app->user->identity->posto_id])
        ->andWhere(['registro.status' => 1])
        ->all();

$table = '
<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <td colspan="6" class="text-bold text-uppercase" style="vertical-align: middle;">Gasolina</td>
        </tr>
        <tr>
            <td class="text-bold" style="vertical-align: middle;">Bomba</td>
            <td class="text-bold" style="vertical-align: middle;">Bico</td>
            <td class="text-bold" style="vertical-align: middle;">Valor</td>
            <td class="text-bold" style="vertical-align: middle;">Quantidade</td>
            <td class="text-bold" style="vertical-align: middle;">Data</td>
            <td class="text-bold" style="vertical-align: middle;">Total R$</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

foreach ($modelsGasolina as $modelGasolina) {

    $table .= '<tr>'
            . '<td style="vertical-align: middle;">' . $modelGasolina->bico->bomba->descricao . '</td>'
            . '<td style="vertical-align: middle;">' . $modelGasolina->bico->descricao . '</td>'
            . '<td style="vertical-align: middle;">R$ ' . number_format($modelGasolina->valor, 2, ',', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . number_format((($modelGasolina->registro_atual - $modelGasolina->registro_anterior) - $modelGasolina->retorno), 0, '.', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelGasolina->registro->data)) . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format((($modelGasolina->registro_atual - $modelGasolina->registro_anterior) - $modelGasolina->retorno) * $modelGasolina->valor, 2, ',', '.') . '</td>'
            . '</tr>';

    $totalGasolina += (($modelGasolina->registro_atual - $modelGasolina->registro_anterior) - $modelGasolina->retorno) * $modelGasolina->valor;
}

$table .= '<tr><td colspan="6" class="text-bold" style="vertical-align: middle;">Total Gasolina: R$ ' . number_format($totalGasolina, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsGasolina)
    $mpdf->WriteHTML($table);

$modelsDiesel = BicoRegistro::find()
        ->leftJoin('registro', 'registro.registro_id = bico_registro.registro_id')
        ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
        ->where(['between', 'registro.data', $model->data_inicial, $model->data_final])
        ->andWhere(['bico.tipo_combustivel_id' => 2])
        ->andWhere(['registro.posto_id' => Yii::$app->user->identity->posto_id])
        ->andWhere(['registro.status' => 1])
        ->all();

$table = '
<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <td colspan="6" class="text-bold text-uppercase" style="vertical-align: middle;">Diesel</td>
        </tr>
        <tr>
            <td class="text-bold" style="vertical-align: middle;">Bomba</td>
            <td class="text-bold" style="vertical-align: middle;">Bico</td>
            <td class="text-bold" style="vertical-align: middle;">Valor</td>
            <td class="text-bold" style="vertical-align: middle;">Quantidade</td>
            <td class="text-bold" style="vertical-align: middle;">Data</td>
            <td class="text-bold" style="vertical-align: middle;">Total R$</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

foreach ($modelsDiesel as $modelDiesel) {

    $table .= '<tr>'
            . '<td style="vertical-align: middle;">' . $modelDiesel->bico->bomba->descricao . '</td>'
            . '<td style="vertical-align: middle;">' . $modelDiesel->bico->descricao . '</td>'
            . '<td style="vertical-align: middle;">R$ ' . number_format($modelDiesel->valor, 2, ',', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . number_format((($modelDiesel->registro_atual - $modelDiesel->registro_anterior) - $modelDiesel->retorno), 0, '.', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelDiesel->registro->data)) . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format((($modelDiesel->registro_atual - $modelDiesel->registro_anterior) - $modelDiesel->retorno) * $modelDiesel->valor, 2, ',', '.') . '</td>'
            . '</tr>';

    $totalDiesel += (($modelDiesel->registro_atual - $modelDiesel->registro_anterior) - $modelDiesel->retorno) * $modelDiesel->valor;
}

$table .= '<tr><td colspan="6" class="text-bold" style="vertical-align: middle;">Total Diesel: R$ ' . number_format($totalDiesel, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsDiesel)
    $mpdf->WriteHTML($table);

foreach ($modelsProduto as $modelProduto) {

    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="6" class="text-bold text-uppercase" style="vertical-align: middle;">' . $modelProduto->descricao . '</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Valor</td>
                <td class="text-bold" style="vertical-align: middle;">Quantidade</td>
                <td class="text-bold" style="vertical-align: middle;">Nota Fiscal</td>
                <td class="text-bold" style="vertical-align: middle;">Data</td>
                <td class="text-bold" style="vertical-align: middle;">Observações</td>
                <td class="text-bold" style="vertical-align: middle;">Total R$</td>
            </tr>
        </thead>
    ';

    $table .= '<tbody>';

    foreach ($modelsProdutoNegociacao as $modelProdutoNegociacao) {

        if ($modelProduto->produto_id == $modelProdutoNegociacao->produto_id) {
            $observacao = $modelProdutoNegociacao->observacao != NULL ? $modelProdutoNegociacao->observacao : 'Não inserido';
            $table .= '<tr>'
                    . '<td style="vertical-align: middle;">R$ ' . number_format($modelProdutoNegociacao->valor, 2, ',', '.') . '</td>'
                    . '<td style="vertical-align: middle;">' . number_format($modelProdutoNegociacao->qtde, 0, '.', '.') . '</td>'
                    . '<td style="vertical-align: middle;">' . $modelProdutoNegociacao->nota_fiscal . '</td>'
                    . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelProdutoNegociacao->data)) . '</td>'
                    . '<td style="vertical-align: middle;">' . $observacao . '</td>'
                    . '<td class="text-bold" style="vertical-align: middle;">R$ ' . $modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor . '</td>'
                    . '</tr>';

            $totalProduto[$modelProduto->produto_id] += $modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor;
            $totalGeral += $modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor;
        }
    }

    $table .= '<tr><td colspan="6" class="text-bold" style="vertical-align: middle;">Total ' . $modelProduto->descricao . ': R$ ' . number_format($totalProduto[$modelProduto->produto_id], 2, ',', '.') . '</td></tr>';

    $table .= '</tbody>';

    $table .= '</table>';

    $mpdf->WriteHTML($table);
}

$table = '
<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <td colspan="2" class="text-bold text-uppercase" style="vertical-align: middle;">Resumo Geral</td>
        </tr>
        <tr>
            <td class="text-bold" style="vertical-align: middle;">Categoria</td>
            <td class="text-bold" style="vertical-align: middle;">Valor</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

if ($modelsGasolina)
    $table .= '<tr><td>Gasolina</td><td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalGasolina, 2, ',', '.') . '</td></tr>';

if ($modelsDiesel)
    $table .= '<tr><td>Diesel</td><td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalDiesel, 2, ',', '.') . '</td></tr>';

foreach ($modelsProduto as $modelProduto) {
    $table .= '<tr>'
            . '<td style="vertical-align: middle;">' . $modelProduto->descricao . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalProduto[$modelProduto->produto_id], 2, ',', '.') . '</td>'
            . '</tr>';
}

$table .= '<tr><td colspan="2" class="text-bold" style="vertical-align: middle;">Total Geral: R$ ' . number_format($totalGeral + $totalGasolina + $totalDiesel, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsProdutoNegociacao || $modelsGasolina || $modelsDiesel)
    $mpdf->WriteHTML($table);

if (!$modelsProdutoNegociacao && !$modelsGasolina && !$modelsDiesel)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
