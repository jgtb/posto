<?php

use app\models\Cliente;
use app\models\CaminhaoCliente;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Alugueis');

$mpdf->WriteHTML('<h2 class="text-center">Relatório #Alugueis</h2>');

$mpdf->WriteHTML('<h3 class="text-center">Carro Tanque</h3>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');


$modelsCliente = Cliente::find()
        ->leftJoin('caminhao_cliente', 'cliente.cliente_id = caminhao_cliente.cliente_id')
        ->where(['between', 'caminhao_cliente.data', $model->data_inicial, $model->data_final])
        ->andWhere(['caminhao_cliente.status' => 1])
        ->orderBy(['caminhao_cliente.data' => SORT_DESC])
        ->all();

$modelsCaminhaoCliente = CaminhaoCliente::find()
        ->where(['between', 'caminhao_cliente.data', $model->data_inicial, $model->data_final])
        ->andWhere(['caminhao_cliente.status' => 1])
        ->orderBy(['caminhao_cliente.data' => SORT_DESC])
        ->all();

foreach ($modelsCliente as $modelCliente) {

    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="7" class="text-bold text-uppercase" style="vertical-align: middle;">' . $modelCliente->nome . '</td>
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

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2" class="text-bold text-uppercase" style="vertical-align: middle;">Resumo Geral</td>
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

$table .= '<tr><td colspan="2" class="text-bold" style="vertical-align: middle;">Total Geral: R$ ' . number_format($totalClienteGeral, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsCaminhaoCliente)
    $mpdf->WriteHTML($table);

if (!$modelsCaminhaoCliente)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
