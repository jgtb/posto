<?php
use app\models\TipoDespesa;
use app\models\Despesa;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Despesas Posto');

$mpdf->WriteHTML('<h2 class="text-center">Relatório de Despesas Posto</h2>');

$mpdf->WriteHTML('<h3 class="text-center">Posto #' . Yii::$app->user->identity->getPosto() . '</h3>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');

$modelsTipoDespesa = TipoDespesa::find()
        ->leftJoin('despesa', 'tipo_despesa.tipo_despesa_id = despesa.tipo_despesa_id')
        ->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])
        ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id, 'referencial' => 1, 'despesa.status' => 1])
        ->all();
$modelsDespesa = Despesa::find()
        ->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])
        ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id, 'referencial' => 1, 'status' => 1])
        ->all();

foreach ($modelsTipoDespesa as $modelTipoDespesa) {
    
    $table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="3">' . $modelTipoDespesa->descricao . '</td>
            </tr>
            <tr>
                <td>Valor</td>
                <td>Data do Pagamento</td>
                <td>Observação</td>
            </tr>
        </thead>
    ';

    $table .= '<tbody>';

    foreach ($modelsDespesa as $modelDespesa) {
        $observacao = $modelDespesa->observacao != NULL ? $modelDespesa->observacao : 'Não inserido';
        if ($modelDespesa->tipo_despesa_id == $modelTipoDespesa->tipo_despesa_id)
        {
            $table .= '<tr>'
                . '<td>R$ ' . number_format($modelDespesa->valor, 2, ',', '.') . '</td>'
                . '<td>' . date('d/m/Y', strtotime($modelDespesa->data_pagamento)) . '</td>'
                . '<td>' . $observacao . '</td>'
                . '</tr>';
            $totalTipoDespesa[$modelTipoDespesa->tipo_despesa_id] += $modelDespesa->valor;
            $totalGeral += $modelDespesa->valor;
        }
    }

    $table .= '<tr><td colspan="3">Total: R$ ' . number_format($totalTipoDespesa[$modelTipoDespesa->tipo_despesa_id], 2, ',', '.') . '</td></tr>';

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

foreach ($modelsTipoDespesa as $modelTipoDespesa) {
    $table .= '<tr>'
            . '<td>' . $modelTipoDespesa->descricao . '</td>'
            . '<td>R$ ' . number_format($totalTipoDespesa[$modelTipoDespesa->tipo_despesa_id], 2, ',', '.') . '</td>'
            . '</tr>';
}

$table .= '<tr><td colspan="2">Total: R$ ' . number_format($totalGeral, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsDespesa) $mpdf->WriteHTML($table);

if (!$modelsDespesa) $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
