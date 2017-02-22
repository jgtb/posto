<?php

use app\models\TipoDespesa;
use app\models\Despesa;
use app\models\Cliente;
use app\models\CaminhaoCliente;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Fechamento Carro Tanque');

$mpdf->WriteHTML('<h2 class="text-center">Relatório #Fechamento</h2>');

$mpdf->WriteHTML('<h3 class="text-center">Carro Tanque</h3>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');

$modelsCliente = Cliente::find()
        ->leftJoin('caminhao_cliente', 'cliente.cliente_id = caminhao_cliente.cliente_id')
        ->where(['between', 'caminhao_cliente.data', $model->data_inicial, $model->data_final])
        ->all();

$modelsCaminhaoCliente = CaminhaoCliente::find()
        ->where(['between', 'caminhao_cliente.data', $model->data_inicial, $model->data_final])
        ->all();

foreach ($modelsCliente as $modelCliente) {

    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="7" class="text-bold text-uppercase" style="vertical-align: middle;">Alugueis #' . $modelCliente->nome . '</td>
            </tr>
            <tr>
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

        if ($modelCaminhaoCliente->cliente_id == $modelCliente->cliente_id) {
            $table .= '<tr>'
                    . '<td style="vertical-align: middle;">' . $modelCaminhaoCliente->tipoCombustivel->descricao . '</td>'
                    . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelCaminhaoCliente->data)) . '</td>'
                    . '<td style="vertical-align: middle;">' . number_format($modelCaminhaoCliente->valor_carrada, 0, '.', '.') . '</td>'
                    . '<td style="vertical-align: middle;">R$ ' . number_format($modelCaminhaoCliente->valor_litro, 2, ',', '.') . '</td>'
                    . '<td style="vertical-align: middle;">R$ ' . number_format($modelCaminhaoCliente->valor_frete, 2, ',', '.') . '</td>'
                    . '<td style="vertical-align: middle;">' . $modelCaminhaoCliente->nota_fiscal . '</td>'
                    . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format(($modelCaminhaoCliente->valor_frete * $modelCaminhaoCliente->valor_carrada), 2, ',', '.') . '</td>'
                    . '</tr>';

            $totalCliente[$modelCliente->cliente_id] += ($modelCaminhaoCliente->valor_frete * $modelCaminhaoCliente->valor_carrada);
            $totalClienteGeral += ($modelCaminhaoCliente->valor_frete * $modelCaminhaoCliente->valor_carrada);
        }
    }

    $table .= '<tr><td colspan="7" class="text-bold" style="vertical-align: middle;">Total ' . $modelCliente->nome . ': R$ ' . number_format($totalCliente[$modelCliente->cliente_id], 2, ',', '.') . '</td></tr>';

    $table .= '</tbody>';

    $table .= '</table>';

    $mpdf->WriteHTML($table);
}

$modelsTipoDespesa = TipoDespesa::find()
        ->leftJoin('despesa', 'tipo_despesa.tipo_despesa_id = despesa.tipo_despesa_id')
        ->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])
        ->andWhere(['despesa.referencial' => 2, 'despesa.status' => 1])
        ->all();

$modelsDespesa = Despesa::find()
        ->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])
        ->andWhere(['referencial' => 2, 'status' => 1])
        ->all();

foreach ($modelsTipoDespesa as $modelTipoDespesa) {

    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="4" class="text-bold text-uppercase" style="vertical-align: middle;">Despesas #' . $modelTipoDespesa->descricao . '</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Valor #Total</td>
                <td class="text-bold" style="vertical-align: middle;">Data #Vencimento</td>
                <td class="text-bold" style="vertical-align: middle;">Data #Pagamento</td>
                <td class="text-bold" style="vertical-align: middle;">Observações</td>
            </tr>
        </thead>
    ';

    $table .= '<tbody>';

    foreach ($modelsDespesa as $modelDespesa) {
        $observacao = $modelDespesa->observacao != NULL ? $modelDespesa->observacao : 'Não inserido';
        if ($modelDespesa->tipo_despesa_id == $modelTipoDespesa->tipo_despesa_id) {
            $table .= '<tr>'
                    . '<td style="vertical-align: middle;">R$ ' . number_format($modelDespesa->valor, 2, ',', '.') . '</td>'
                    . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelDespesa->data_vencimento)) . '</td>'
                    . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelDespesa->data_pagamento)) . '</td>'
                    . '<td style="vertical-align: middle;">' . $observacao . '</td>'
                    . '</tr>';
            $totalTipoDespesa[$modelTipoDespesa->tipo_despesa_id] += $modelDespesa->valor;
            $totalDespesa += $modelDespesa->valor;
        }
    }

    $table .= '<tr><td colspan="4" class="text-bold" style="vertical-align: middle;">Total ' . $modelTipoDespesa->descricao . ': R$ ' . number_format($totalTipoDespesa[$modelTipoDespesa->tipo_despesa_id], 2, ',', '.') . '</td></tr>';

    $table .= '</tbody>';

    $table .= '</table>';

    $mpdf->WriteHTML($table);
}

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2" class="text-bold text-uppercase" style="vertical-align: middle;">Total #Alugueis</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Cliente</td>
                <td class="text-bold" style="vertical-align: middle;">Total</td>
            </tr>
        </thead>
    ';

$table .= '<tbody>';

foreach ($modelsCliente as $modelCliente) {
    $table .= '<tr>'
            . '<td>' . $modelCliente->nome . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalCliente[$modelCliente->cliente_id], 2, ',', '.') . '</td>'
            . '</tr>';
}

$table .= '<tr><td colspan="2" class="text-bold" style="vertical-align: middle;">Total Geral #Clientes: R$ ' . number_format($totalClienteGeral, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsCaminhaoCliente)
    $mpdf->WriteHTML($table);

$table = '
<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <td colspan="2" class="text-bold text-uppercase">Total #Despesas</td>
        </tr>
        <tr>
            <td class="text-bold">Categoria</td>
            <td class="text-bold">Valor</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

foreach ($modelsTipoDespesa as $modelTipoDespesa) {
    $table .= '<tr>'
            . '<td>' . $modelTipoDespesa->descricao . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalTipoDespesa[$modelTipoDespesa->tipo_despesa_id], 2, ',', '.') . '</td>'
            . '</tr>';
}

$table .= '<tr><td colspan="2" class="text-bold">Total Geral #Despesas: R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsDespesa)
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

$table .= '<tr><td>Total Despesa</td><td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td></tr>';
$table .= '<tr><td>Total Receita Bruta</td><td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalClienteGeral, 2, ',', '.') . '</td></tr>';
$table .= '<tr><td>Total Receita Líquida</td><td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalClienteGeral - $totalDespesa, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsCaminhaoCliente || $modelsDespesa)
    $mpdf->WriteHTML($table);

if (!$modelsCaminhaoCliente && !$modelsDespesa)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
