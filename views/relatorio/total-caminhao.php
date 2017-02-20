<?php

use app\models\TipoDespesa;
use app\models\Despesa;
use app\models\Caminhao;
use app\models\CaminhaoCliente;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Fechamento Carro Tanque');

$mpdf->WriteHTML('<h2 class="text-center">Relatório #Carro Tanque</h2>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');

$modelsCaminhao = Caminhao::find()
        ->leftJoin('caminhao_cliente', 'caminhao.caminhao_id = caminhao_cliente.caminhao_id')
        ->where(['between', 'caminhao_cliente.data', $model->data_inicial, $model->data_final])
        ->all();

$modelsCaminhaoCliente = CaminhaoCliente::find()
        ->where(['between', 'caminhao_cliente.data', $model->data_inicial, $model->data_final])
        ->all();

foreach ($modelsCaminhao as $modelCaminhao) {

    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="8" class="text-bold text-uppercase" style="vertical-align: middle;">' . $modelCaminhao->descricao . '</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Cliente</td>
                <td class="text-bold" style="vertical-align: middle;">Combustível</td>
                <td class="text-bold" style="vertical-align: middle;">Data</td>
                <td class="text-bold" style="vertical-align: middle;">Quantidade #Litro</td>
                <td class="text-bold" style="vertical-align: middle;">Valor #Litro</td>
                <td class="text-bold" style="vertical-align: middle;">Valor #Frete</td>
                <td class="text-bold" style="vertical-align: middle;">Nota Fiscal</td>
                <td class="text-bold" style="vertical-align: middle;">Total</td>
            </tr>
        </thead>
    ';

    $table .= '<tbody>';

    foreach ($modelsCaminhaoCliente as $modelCaminhaoCliente) {

        $table .= '<tr>'
                . '<td style="vertical-align: middle;">' . $modelCaminhaoCliente->cliente->nome . '</td>'
                . '<td style="vertical-align: middle;">' . $modelCaminhaoCliente->tipoCombustivel->descricao . '</td>'
                . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelCaminhaoCliente->data)) . '</td>'
                . '<td style="vertical-align: middle;">' . number_format($modelCaminhaoCliente->valor_carrada, 0, '.', '.') . '</td>'
                . '<td style="vertical-align: middle;">R$ ' . number_format($modelCaminhaoCliente->valor_litro, 2, ',', '.') . '</td>'
                . '<td style="vertical-align: middle;">R$ ' . number_format($modelCaminhaoCliente->valor_frete, 2, ',', '.') . '</td>'
                . '<td style="vertical-align: middle;">' . $modelCaminhaoCliente->nota_fiscal . '</td>'
                . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format(($modelCaminhaoCliente->valor_frete * $modelCaminhaoCliente->valor_carrada), 2, ',', '.') . '</td>'
                . '</tr>';
        $totalCaminhao[$modelCaminhao->caminhao_id] += ($modelCaminhaoCliente->valor_frete * $modelCaminhaoCliente->valor_carrada);
        $totalCaminhaoGeral += ($modelCaminhaoCliente->valor_frete * $modelCaminhaoCliente->valor_carrada);
    }

    $table .= '<tr><td colspan="8" class="text-bold" style="vertical-align: middle;">Total: R$ ' . number_format($totalCaminhao[$modelCaminhao->caminhao_id], 2, ',', '.') . '</td></tr>';

    $table .= '</tbody>';

    $table .= '</table>';

    $mpdf->WriteHTML($table);
}

$modelsTipoDespesa = TipoDespesa::find()
        ->leftJoin('despesa', 'tipo_despesa.tipo_despesa_id = despesa.tipo_despesa_id')
        ->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])
        ->andWhere(['referencial' => 2, 'despesa.status' => 1])
        ->all();

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2" class="text-bold text-uppercase" style="vertical-align: middle;">Despesas</td>
            </tr>
            <tr>
                <td class="text-bold">Categoria</td>
                <td class="text-bold">Total</td>
            </tr>
        </thead>
    ';

$table .= '<tbody>';

foreach ($modelsTipoDespesa as $modelTipoDespesa) {
    $totalTipoDespesa = Despesa::find()->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])->andWhere(['referencial' => 2, 'status' => 1])->sum('despesa.valor');
    $table .= '<tr>'
            . '<td>' . $modelTipoDespesa->descricao . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalTipoDespesa, 2, ',', '.') . '</td>'
            . '</tr>';
    $totalDespesa += $totalTipoDespesa;
}

$table .= '<tr><td colspan="2" class="text-bold" style="vertical-align: middle;">Total: R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsTipoDespesa)
    $mpdf->WriteHTML($table);

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2" class="text-bold text-uppercase" style="vertical-align: middle;">Resumo Geral</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Categoria</td>
                <td class="text-bold" style="vertical-align: middle;">Total</td>
            </tr>
        </thead>
    ';

$table .= '<tbody>';

$table .= '<tr><td>Total Despesa</td><td class="text-bold" style="vertical-align: middle;">Total: R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td></tr>';
$table .= '<tr><td>Total Receita Bruta</td><td class="text-bold" style="vertical-align: middle;">Total: R$ ' . number_format($totalCaminhaoGeral, 2, ',', '.') . '</td></tr>';
$table .= '<tr><td>Total Receita Líquida</td><td class="text-bold" style="vertical-align: middle;">Total: R$ ' . number_format($totalCaminhaoGeral - $totalDespesa, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';


$mpdf->WriteHTML($table);

if ($modelsDespesa)
    $mpdf->WriteHTML($table);

if (!$modelsTipoDespesa && $modelsCaminhao)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
