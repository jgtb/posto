<?php

use app\models\Produto;
use app\models\ProdutoNegociacao;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Compras');

$mpdf->WriteHTML('<h2 class="text-center">Relatório de Compras</h2>');

$mpdf->WriteHTML('<h3 class="text-center">Posto #' . Yii::$app->user->identity->getPosto() . '</h3>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');

$modelsProduto = Produto::find()
        ->leftJoin('produto_negociacao', 'produto.produto_id = produto_negociacao.produto_id')
        ->where(['between', 'produto_negociacao.data', $model->data_inicial, $model->data_final])
        ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id])
        ->andWhere(['negociacao_id' => 2])
        ->andWhere(['produto_negociacao.status' => 1])
        ->all();

$modelsProdutoNegociacao = ProdutoNegociacao::find()
        ->where(['between', 'data', $model->data_inicial, $model->data_final])
        ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id])
        ->andWhere(['negociacao_id' => 2])
        ->andWhere(['produto_negociacao.status' => 1])
        ->all();

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
                    . '<td>R$ ' . number_format($modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor, 2, ',', '.') . '</td>'
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

foreach ($modelsProduto as $modelProduto) {
    $table .= '<tr>'
            . '<td>' . $modelProduto->descricao . '</td>'
            . '<td>R$ ' . number_format($totalProduto[$modelProduto->produto_id], 2, ',', '.') . '</td>'
            . '</tr>';
}

$table .= '<tr><td colspan="2">Total: R$ ' . number_format($totalGeral, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsProdutoNegociacao)
    $mpdf->WriteHTML($table);

if (!$modelsProdutoNegociacao)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
