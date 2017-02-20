<?php

use app\models\CaminhaoCliente;
use app\models\TipoCombustivel;

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

$modelsTipoCombustivel = TipoCombustivel::find()->all();
$modelsCaminhaoCliente = CaminhaoCliente::find()
        ->where(['between', 'data', $model->data_inicial, $model->data_final])
        ->andWhere(['status' => 1])
        ->all();

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="8" class="text-uppercase text-bold">Alugueis</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Data</td>
                <td class="text-bold" style="vertical-align: middle;">Cliente</td>
                <td class="text-bold" style="vertical-align: middle;">Combustível</td>
                <td class="text-bold" style="vertical-align: middle;">Quantidade #Litro</td>
                <td class="text-bold" style="vertical-align: middle;">Valor #Litro</td>
                <td class="text-bold" style="vertical-align: middle;">Valor #Frete</td>
                <td class="text-bold" style="vertical-align: middle;">Nota Fiscal</td>
                <td class="text-bold" style="vertical-align: middle;">Total</td>
            </tr>
        </thead>
    ';

$table .= '<tbody>';

foreach ($modelsTipoCombustivel as $modelTipoCombustivel) {
    $totalTipoCombustivel = [];
    foreach ($modelsCaminhaoCliente as $modelCaminhaoCliente) {

        if ($modelTipoCombustivel->tipo_combustivel_id == $modelCaminhaoCliente->tipo_combustivel_id) {

            $table .= '<tr>'
                    . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelCaminhaoCliente->data)) . '</td>'
                    . '<td style="vertical-align: middle;">' . $modelCaminhaoCliente->cliente->nome . '</td>'
                    . '<td style="vertical-align: middle;">' . $modelTipoCombustivel->descricao . '</td>'
                    . '<td style="vertical-align: middle;">' . number_format($modelCaminhaoCliente->valor_carrada, 0, '.', '.') . '</td>'
                    . '<td style="vertical-align: middle;">R$ ' . number_format($modelCaminhaoCliente->valor_litro, 2, ',', '.') . '</td>'
                    . '<td style="vertical-align: middle;">R$ ' . number_format($modelCaminhaoCliente->valor_frete, 2, ',', '.') . '</td>'
                    . '<td style="vertical-align: middle;">' . $modelCaminhaoCliente->nota_fiscal . '</td>'
                    . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($modelCaminhaoCliente->valor_carrada * $modelCaminhaoCliente->valor_frete, 2, ',', '.') . '</td>'
                    . '</tr>';

            $totalTipoCombustivel[$modelTipoCombustivel->tipo_combustivel_id] += $modelCaminhaoCliente->valor_carrada * $modelCaminhaoCliente->valor_frete;
            $totalTipoCombustivelGeral[$modelTipoCombustivel->tipo_combustivel_id] += $modelCaminhaoCliente->valor_carrada * $modelCaminhaoCliente->valor_frete;
        }
    }
    $table .= '<tr><td colspan="8" class="text-bold">Total ' . $modelTipoCombustivel->descricao . ' : R$ ' . number_format($totalTipoCombustivel[$modelTipoCombustivel->tipo_combustivel_id], 2, ',', '.') . '</td></tr>';
}

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);

$table = '
<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <td colspan="2" class="text-bold text-uppercase">Total Geral</td>
        </tr>
        <tr>
            <td class="text-bold">Combustível</td>
            <td class="text-bold">Valor</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

foreach ($modelsTipoCombustivel as $modelTipoCombustivel) {
    $table .= '<tr>'
            . '<td>' . $modelTipoCombustivel->descricao . '</td>'
            . '<td class="text-bold">R$ ' . number_format($totalTipoCombustivelGeral[$modelTipoCombustivel->tipo_combustivel_id], 2, ',', '.') . '</td>'
            . '</tr>';
    $totalGeral += $totalTipoCombustivelGeral[$modelTipoCombustivel->tipo_combustivel_id];
}

$table .= '<tr><td colspan="2" class="text-bold">Total: R$ ' . number_format($totalGeral, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsCaminhaoCliente)
    $mpdf->WriteHTML($table);

if (!$modelsCaminhaoCliente)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
