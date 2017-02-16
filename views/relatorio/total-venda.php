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

$mpdf->WriteHTML('<h2 class="text-center">Relatório de Vendas</h2>');

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
            <td colspan="6">Gasolina</td>
        </tr>
        <tr>
            <td>Bomba</td>
            <td>Bico</td>
            <td>Valor</td>
            <td>Quantidade</td>
            <td>Data</td>
            <td>Total R$</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

foreach ($modelsGasolina as $modelGasolina) {

    $table .= '<tr>'
            . '<td>' . $modelGasolina->bico->bomba->descricao . '</td>'
            . '<td>' . $modelGasolina->bico->descricao . '</td>'
            . '<td>R$ ' . number_format($modelGasolina->valor, 2, ',', '.') . '</td>'
            . '<td>' . (($modelGasolina->registro_atual - $modelGasolina->registro_anterior) - $modelGasolina->retorno) . '</td>'
            . '<td>' . date('d/m/Y', strtotime($modelGasolina->registro->data)) . '</td>'
            . '<td>R$ ' . number_format((($modelGasolina->registro_atual - $modelGasolina->registro_anterior) - $modelGasolina->retorno) * $modelGasolina->valor, 2, ',', '.') . '</td>'
            . '</tr>';

    $totalGasolina += (($modelGasolina->registro_atual - $modelGasolina->registro_anterior) - $modelGasolina->retorno) * $modelGasolina->valor;
}

$table .= '<tr><td colspan="6">Total: R$ ' . number_format($totalGasolina, 2, ',', '.') . '</td></tr>';

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
            <td colspan="6">Diesel</td>
        </tr>
        <tr>
            <td>Bomba</td>
            <td>Bico</td>
            <td>Valor</td>
            <td>Quantidade</td>
            <td>Data</td>
            <td>Total R$</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

foreach ($modelsDiesel as $modelDiesel) {

    $table .= '<tr>'
            . '<td>' . $modelDiesel->bico->bomba->descricao . '</td>'
            . '<td>' . $modelDiesel->bico->descricao . '</td>'
            . '<td>R$ ' . number_format($modelDiesel->valor, 2, ',', '.') . '</td>'
            . '<td>' . (($modelDiesel->registro_atual - $modelDiesel->registro_anterior) - $modelDiesel->retorno)  . '</td>'
            . '<td>' . date('d/m/Y', strtotime($modelDiesel->registro->data)) . '</td>'
            . '<td>R$ ' . number_format((($modelDiesel->registro_atual - $modelDiesel->registro_anterior) - $modelDiesel->retorno) * $modelDiesel->valor, 2, ',', '.') . '</td>'
            . '</tr>';

    $totalDiesel += (($modelDiesel->registro_atual - $modelDiesel->registro_anterior) - $modelDiesel->retorno) * $modelDiesel->valor;
}

$table .= '<tr><td colspan="6">Total: R$ ' . number_format($totalDiesel, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsDiesel)
    $mpdf->WriteHTML($table);

foreach ($modelsProduto as $modelProduto) {

    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="4">' . $modelProduto->descricao . '</td>
            </tr>
            <tr>
                <td>Valor</td>
                <td>Quantidade</td>
                <td>Data</td>
                <td>Total R$</td>
            </tr>
        </thead>
    ';

    $table .= '<tbody>';

    foreach ($modelsProdutoNegociacao as $modelProdutoNegociacao) {

        if ($modelProduto->produto_id == $modelProdutoNegociacao->produto_id) {
            $table .= '<tr>'
                    . '<td>R$ ' . number_format($modelProdutoNegociacao->valor, 2, ',', '.') . '</td>'
                    . '<td>' . $modelProdutoNegociacao->qtde . '</td>'
                    . '<td>' . date('d/m/Y', strtotime($modelProdutoNegociacao->data)) . '</td>'
                    . '<td>R$ ' . $modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor . '</td>'
                    . '</tr>';

            $totalProduto[$modelProduto->produto_id] += $modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor;
            $totalGeral += $modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor;
        }
    }

    $table .= '<tr><td colspan="4">Total: R$ ' . number_format($totalProduto[$modelProduto->produto_id], 2, ',', '.') . '</td></tr>';

    $table .= '</tbody>';

    $table .= '</table>';

    $mpdf->WriteHTML($table);
}

$table = '
<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <td colspan="2">Total Geral</td>
        </tr>
        <tr>
            <td>Categoria</td>
            <td>Valor</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

if ($modelsGasolina)
    $table .= '<tr><td>Gasolina</td><td>R$ ' . number_format($totalGasolina, 2, ',', '.') . '</td></tr>';

if ($modelsDiesel)
    $table .= '<tr><td>Diesel</td><td>R$ ' . number_format($totalDiesel, 2, ',', '.') . '</td></tr>';

foreach ($modelsProduto as $modelProduto) {
    $table .= '<tr>'
            . '<td>' . $modelProduto->descricao . '</td>'
            . '<td>R$ ' . number_format($totalProduto[$modelProduto->produto_id], 2, ',', '.') . '</td>'
            . '</tr>';
}


$table .= '<tr><td colspan="2">Total: R$ ' . number_format($totalGeral + $totalGasolina + $totalDiesel, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsProdutoNegociacao || $modelsGasolina || $modelsDiesel)
    $mpdf->WriteHTML($table);

if (!$modelsProdutoNegociacao && !$modelsGasolina && !$modelsDiesel)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
