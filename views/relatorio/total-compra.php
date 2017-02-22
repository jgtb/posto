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

$mpdf->WriteHTML('<h2 class="text-center">Relatório #Compras</h2>');

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
        ->orderBy(['produto_negociacao.data' => SORT_DESC])
        ->all();

$modelsProdutoNegociacao = ProdutoNegociacao::find()
        ->where(['between', 'data', $model->data_inicial, $model->data_final])
        ->andWhere(['posto_id' => Yii::$app->user->identity->posto_id])
        ->andWhere(['negociacao_id' => 2])
        ->andWhere(['produto_negociacao.status' => 1])
        ->orderBy(['produto_negociacao.data' => SORT_DESC])
        ->all();

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
                    . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($modelProdutoNegociacao->qtde * $modelProdutoNegociacao->valor, 2, ',', '.') . '</td>'
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

foreach ($modelsProduto as $modelProduto) {
    $table .= '<tr>'
            . '<td style="vertical-align: middle;">' . $modelProduto->descricao . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalProduto[$modelProduto->produto_id], 2, ',', '.') . '</td>'
            . '</tr>';
}

$table .= '<tr><td colspan="2" class="text-bold" style="vertical-align: middle;">Total Geral: R$ ' . number_format($totalGeral, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

if ($modelsProdutoNegociacao)
    $mpdf->WriteHTML($table);

if (!$modelsProdutoNegociacao)
    $mpdf->WriteHTML('<h4 class="text-center">Nenhum resultado encontrado.</h4>');

$mpdf->Output();
exit;
