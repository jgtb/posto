<?php

use app\models\Despesa;
use app\models\ValorSaida;
use app\models\BicoRegistro;

include("../vendor/mpdf/mpdf/mpdf.php");

$mpdf = new mPDF('utf-8', 'A4', '', 'timesnewroman', 10, 20, 20, 20, '', 'P');

$css = file_get_contents('../vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css');
$mpdf->WriteHTML($css, 1);
$css = file_get_contents('../web/css/relatorio.css');
$mpdf->WriteHTML($css, 1);

$mpdf->SetTitle('Relatorio #Fechamento');

$mpdf->WriteHTML('<h2 class="text-center">Relatório #Fechamento</h2>');

$mpdf->WriteHTML('<h3 class="text-center">Posto #' . Yii::$app->user->identity->getPosto() . '</h3>');

$mpdf->WriteHTML('<h4 class="text-center">Período:</h4>');

$mpdf->WriteHTML('<h5 class="text-center">De: ' . date('d/m/Y', strtotime($model->data_inicial)) . '</h5>');
$mpdf->WriteHTML('<h5 class="text-center">Até: ' . date('d/m/Y', strtotime($model->data_final)) . '</h5>');


//Fechamento
$estoqueGasolinaInicial = $model->getEstoqueInicial(1);
$estoqueGasolinaFinal = $model->getEstoqueFinal(1);

$estoqueDieselInicial = $model->getEstoqueInicial(2);
$estoqueDieselFinal = $model->getEstoqueFinal(2);

$entradaGasolina = $model->getEntrada(1);
$saidaGasolina = $model->getSaida(1);

$entradaDiesel = $model->getEntrada(2);
$saidaDiesel = $model->getSaida(2);

$retornoGasolina = $model->getRetorno(1);
$retornoDiesel = $model->getRetorno(2);

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="6" class="text-bold text-uppercase" style="vertical-align: middle;">Fechamento Geral</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Produto</td>
                <td class="text-bold" style="vertical-align: middle;">Estoque Inicial</td>
                <td class="text-bold" style="vertical-align: middle;">Entrada</td>
                <td class="text-bold" style="vertical-align: middle;">Saída</td>
                <td class="text-bold" style="vertical-align: middle;">Retorno</td>
                <td class="text-bold" style="vertical-align: middle;">Estoque Final</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

$table .= '<tr>'
        . '<td style="vertical-align: middle;">Gasolina</td>'
        . '<td style="vertical-align: middle;">' . number_format($estoqueGasolinaInicial, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($entradaGasolina, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($saidaGasolina, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($retornoGasolina, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($estoqueGasolinaFinal, 0, '.', '.') . '</td>'
        . '</tr>';

$table .= '<tr>'
        . '<td style="vertical-align: middle;">Diesel</td>'
        . '<td style="vertical-align: middle;">' . number_format($estoqueDieselInicial, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($entradaDiesel, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($saidaDiesel, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($retornoDiesel, 0, '.', '.') . '</td>'
        . '<td style="vertical-align: middle;">' . number_format($estoqueDieselFinal, 0, '.', '.') . '</td>'
        . '</tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);










//Vendas
$modelsVendaGeral = $model->getVendaGeral();

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="7" class="text-bold text-uppercase" style="vertical-align: middle;">Vendas #Combustível</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Bomba</td>
                <td class="text-bold" style="vertical-align: middle;">Bico</td>
                <td class="text-bold" style="vertical-align: middle;">Produto</td>
                <td class="text-bold" style="vertical-align: middle;">Saldo Inicial</td>
                <td class="text-bold" style="vertical-align: middle;">Saldo Final</td>
                <td class="text-bold" style="vertical-align: middle;">Quantidade #Litro</td>
                <td class="text-bold" style="vertical-align: middle;">Total</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

foreach ($modelsVendaGeral as $modelVendaGeral) {

    $table .= '<tr>'
            . '<td style="vertical-align: middle;">' . $modelVendaGeral->bomba->descricao . '</td>'
            . '<td style="vertical-align: middle;">' . $modelVendaGeral->descricao . '</td>'
            . '<td style="vertical-align: middle;">' . $modelVendaGeral->tipoCombustivel->descricao . '</td>'
            . '<td style="vertical-align: middle;">' . number_format(BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->orderBy(['registro.registro_id' => SORT_ASC])->one()->registro_anterior, 0, '.', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . number_format(BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->orderBy(['registro.registro_id' => SORT_DESC])->one()->registro_atual, 0, '.', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . number_format(BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->sum('((bico_registro.registro_atual - bico_registro.registro_anterior) - bico_registro.retorno)'), 0, '.', '.') . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format(BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->sum('(((bico_registro.registro_atual - bico_registro.registro_anterior) - bico_registro.retorno) * bico_registro.valor)'), 2, ',', '.') . '</td>'
            . '</tr>';
}

$totalVendas = BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->sum('(((bico_registro.registro_atual - bico_registro.registro_anterior) - bico_registro.retorno) * bico_registro.valor)');

$table .= '<tr><td colspan="7" class="text-bold" style="vertical-align: middle;">Total Vendas #Combustível: R$ ' . number_format($totalVendas, 2, ',', '.') . '</td></tr>';

$table .= '<tr><td colspan="7" class="text-bold text-uppercase" style="vertical-align: middle;">Vendas #Outras Receitas</td></tr>';

$table .= '<tr>
        <td class="text-bold" style="vertical-align: middle;">Valor</td>
        <td class="text-bold" style="vertical-align: middle;">Quantidade</td>
        <td class="text-bold" style="vertical-align: middle;">Nota Fiscal</td>
        <td class="text-bold" style="vertical-align: middle;">Data</td>
        <td class="text-bold" style="vertical-align: middle;">Observações</td>
        <td colspan="2" class="text-bold" style="vertical-align: middle;">Total R$</td>
        </tr>';

$modelsVendaOutrosGeral = $model->getVendaOutrosGeral();

foreach ($modelsVendaOutrosGeral as $modelVendaOutrosGeral) {
    $observacao = $modelsVendaOutrosGeral->observacao != NULL ? $modelsVendaOutrosGeral->observacao : 'Não inserido';
    $table .= '<tr>'
            . '<td style="vertical-align: middle;">R$ ' . number_format($modelVendaOutrosGeral->valor, 2, ',', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . number_format($modelVendaOutrosGeral->qtde, 0, '.', '.') . '</td>'
            . '<td style="vertical-align: middle;">' . $modelVendaOutrosGeral->nota_fiscal . '</td>'
            . '<td style="vertical-align: middle;">' . date('d/m/Y', strtotime($modelVendaOutrosGeral->data)) . '</td>'
            . '<td style="vertical-align: middle;">' . $observacao . '</td>'
            . '<td colspan="2" class="text-bold" style="vertical-align: middle;">R$ ' . number_format($modelVendaOutrosGeral->qtde * $modelVendaOutrosGeral->valor, 2, ',', '.') . '</td>'
            . '</tr>';
}

$table .= '<tr><td colspan="7" class="text-bold" style="vertical-align: middle;">Total Vendas #Outras Receitas: R$ ' . number_format($model->getVendaOutrosGeralTotal(), 2, ',', '.') . '</td></tr>';

$totalVendas += $model->getVendaOutrosGeralTotal();

$table .= '<tr><td colspan="7" class="text-bold" style="vertical-align: middle;">Total Vendas #Geral: R$ ' . number_format($totalVendas, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);













//Compras
$modelsCompraGeral = $model->getCompraGeral();

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="4" class="text-bold text-uppercase" style="vertical-align: middle;">Compras</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Produto</td>
                <td class="text-bold" style="vertical-align: middle;">Quantidade</td>
                <td class="text-bold" style="vertical-align: middle;">Valor</td>
                <td class="text-bold" style="vertical-align: middle;">Total</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

foreach ($modelsCompraGeral as $modelCompraGeral) {

    $table .= '<tr>'
            . '<td style="vertical-align: middle;">' . $modelCompraGeral->bicoRegistro->bico->tipoCombustivel->descricao . '</td>'
            . '<td style="vertical-align: middle;">' . number_format(ValorSaida::find()->leftJoin('bico_registro', 'valor_saida.bico_registro_id = bico_registro.bico_registro_id')->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->leftJoin('produto_negociacao', 'valor_saida.produto_negociacao_id = produto_negociacao.produto_negociacao_id')->where(['between', 'DATE(registro.data)', $model->data_inicial, $model->data_final])->andWhere(['produto_negociacao.valor' => $modelCompraGeral->produtoNegociacao->valor, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->sum('valor_saida.valor'), 0, '.', '.') . '</td>'
            . '<td style="vertical-align: middle;">R$ ' . number_format($modelCompraGeral->produtoNegociacao->valor, 4, ',', '.') . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format(ValorSaida::find()->leftJoin('bico_registro', 'valor_saida.bico_registro_id = bico_registro.bico_registro_id')->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->leftJoin('produto_negociacao', 'valor_saida.produto_negociacao_id = produto_negociacao.produto_negociacao_id')->where(['between', 'DATE(registro.data)', $model->data_inicial, $model->data_final])->andWhere(['produto_negociacao.valor' => $modelCompraGeral->produtoNegociacao->valor, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->sum('valor_saida.valor * produto_negociacao.valor'), 2, ',', '.') . '</td>'
            . '</tr>';
}

$totalCompras = ValorSaida::find()->leftJoin('bico_registro', 'valor_saida.bico_registro_id = bico_registro.bico_registro_id')->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->leftJoin('produto_negociacao', 'valor_saida.produto_negociacao_id = produto_negociacao.produto_negociacao_id')->where(['between', 'DATE(registro.data)', $model->data_inicial, $model->data_final])->andWhere(['registro.posto_id' => Yii::$app->user->identity->posto_id])->sum('valor_saida.valor * produto_negociacao.valor');

$table .= '<tr><td colspan="4" class="text-bold" style="vertical-align: middle;">Total Compras: R$ ' . number_format($totalCompras, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);










//Despesas
$modelsDespesaGeral = $model->getDespesaGeral();

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2" class="text-bold text-uppercase" style="vertical-align: middle;">Despesas</td>
            </tr>
            <tr>
                <td class="text-bold" style="vertical-align: middle;">Categoria</td>
                <td class="text-bold" style="vertical-align: middle;">Total</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

foreach ($modelsDespesaGeral as $modelDespesaGeral) {
    $totalTipoDespesa = Despesa::find()->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])->andWhere(['tipo_despesa_id' => $modelDespesaGeral->tipo_despesa_id, 'posto_id' => Yii::$app->user->identity->posto_id, 'status' => 1])->sum('valor');
    $table .= '<tr>'
            . '<td style="vertical-align: middle;">' . $modelDespesaGeral->descricao . '</td>'
            . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalTipoDespesa, 2, ',', '.') . '</td>'
            . '</tr>';

    $totalDespesa += $totalTipoDespesa;
}

$table .= '<tr><td colspan="2" class="text-bold" style="vertical-align: middle;">Total Despesas   : R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);











//Geral
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

$table .= '<tr>'
        . '<td style="vertical-align: middle;">Total de Compras</td>'
        . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalCompras, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td style="vertical-align: middle;">Total de Vendas</td>'
        . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalVendas, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td style="vertical-align: middle;">Total de Despesas</td>'
        . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td style="vertical-align: middle;">Receita Bruta</td>'
        . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format($totalVendas - $totalCompras, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td style="vertical-align: middle;">Receita Líquida</td>'
        . '<td class="text-bold" style="vertical-align: middle;">R$ ' . number_format(($totalVendas - $totalCompras) - $totalDespesa, 2, ',', '.') . '</td>'
        . '</tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);

$mpdf->Output();
exit;
