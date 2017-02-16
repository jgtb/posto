<?php

use app\models\Caminhao;
use app\models\Cliente;
use app\models\CaminhaoCliente;
use app\models\TipoCombustivel;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Alugueis');

$mpdf->WriteHTML('<h2 class="text-center">Relatório de Alugueis</h2>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');

$modelsCaminhao = Caminhao::find()
        ->leftJoin('caminhao_cliente', 'caminhao.caminhao_id = caminhao_cliente.caminhao_id')
        ->where(['between', 'data', $model->data_inicial, $model->data_final])
        ->andWhere(['caminhao_cliente.status' => 1])
        ->all();

$modelsCliente = Cliente::find()
        ->leftJoin('caminhao_cliente', 'cliente.cliente_id = caminhao_cliente.cliente_id')
        ->where(['between', 'data', $model->data_inicial, $model->data_final])
        ->andWhere(['caminhao_cliente.status' => 1])
        ->all();

$modelsTipoCombustivel = TipoCombustivel::find()->all();
$modelsCaminhaoCliente = CaminhaoCliente::find()
        ->where(['between', 'data', $model->data_inicial, $model->data_final])
        ->andWhere(['status' => 1])
        ->all();

foreach ($modelsCaminhao as $modelCaminhao) {

    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="6">' . $modelCaminhao->descricao . '</td>
            </tr>
            <tr>
                <td>Cliente</td>
                <td>Combustível</td>
                <td>Quantidade #Litro</td>
                <td>Valor #Litro</td>
                <td>Valor #Frete</td>
                <td>Total R$</td>
            </tr>
        </thead>
    ';

    $table .= '<tbody>';

    foreach ($modelsTipoCombustivel as $modelTipoCombustivel) {
        $totalTipoCombustivel = [];
        foreach ($modelsCliente as $modelCliente) {

            foreach ($modelsCaminhaoCliente as $modelCaminhaoCliente) {

                if ($modelTipoCombustivel->tipo_combustivel_id == $modelCaminhaoCliente->tipo_combustivel_id && $modelCaminhaoCliente->cliente_id == $modelCliente->cliente_id && $modelCaminhaoCliente->caminhao_id == $modelCaminhao->caminhao_id) {

                    $table .= '<tr>'
                            . '<td>' . $modelCaminhaoCliente->cliente->nome . '</td>'
                            . '<td>' . $modelTipoCombustivel->descricao . '</td>'
                            . '<td>' . $modelCaminhaoCliente->valor_carrada . '</td>'
                            . '<td>R$ ' . number_format($modelCaminhaoCliente->valor_litro, 2, ',', '.') . '</td>'
                            . '<td>R$ ' . number_format($modelCaminhaoCliente->valor_frete, 2, ',', '.') . '</td>'
                            . '<td>R$ ' . number_format($modelCaminhaoCliente->valor_carrada * $modelCaminhaoCliente->valor_frete, 2, ',', '.') . '</td>'
                            . '</tr>';

                    $totalTipoCombustivel[$modelTipoCombustivel->tipo_combustivel_id] += $modelCaminhaoCliente->valor_carrada * $modelCaminhaoCliente->valor_frete;
                    $totalTipoCombustivelGeral[$modelTipoCombustivel->tipo_combustivel_id] += $modelCaminhaoCliente->valor_carrada * $modelCaminhaoCliente->valor_frete;
                }
            }
        }
        $table .= '<tr><td colspan="6">Total ' . $modelTipoCombustivel->descricao . ' : R$ ' . number_format($totalTipoCombustivel[$modelTipoCombustivel->tipo_combustivel_id], 2, ',', '.') . '</td></tr>';
    }

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
            <td>Combustível</td>
            <td>Valor</td>
        </tr>
    </thead>
';

$table .= '<tbody>';

foreach ($modelsTipoCombustivel as $modelTipoCombustivel) {
    $table .= '<tr>'
            . '<td>' . $modelTipoCombustivel->descricao . '</td>'
            . '<td>R$ ' . number_format($totalTipoCombustivelGeral[$modelTipoCombustivel->tipo_combustivel_id], 2, ',', '.') . '</td>'
            . '</tr>';
    $totalGeral += $totalTipoCombustivelGeral[$modelTipoCombustivel->tipo_combustivel_id];
}

$table .= '<tr><td colspan="2">Total: R$ ' . number_format($totalGeral, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsCaminhaoCliente)
    $mpdf->WriteHTML($table);

if (!$modelsCaminhaoCliente)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
