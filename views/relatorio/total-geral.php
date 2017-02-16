<?php

use app\models\Despesa;
use app\models\ProdutoNegociacao;
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
                <td colspan="6">Fechamento Geral</td>
            </tr>
            <tr>
                <td>Produto</td>
                <td>Estoque Inicial</td>
                <td>Entrada</td>
                <td>Saída</td>
                <td>Retorno</td>
                <td>Estoque Final</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

$table .= '<tr>'
        . '<td>Gasolina</td>'
        . '<td>' . $estoqueGasolinaInicial . '</td>'
        . '<td>' . $entradaGasolina . '</td>'
        . '<td>' . $saidaGasolina . '</td>'
        . '<td>' . $retornoGasolina . '</td>'
        . '<td>' . $estoqueGasolinaFinal . '</td>'
        . '</tr>';

$table .= '<tr>'
        . '<td>Diesel</td>'
        . '<td>' . $estoqueDieselInicial . '</td>'
        . '<td>' . $entradaDiesel . '</td>'
        . '<td>' . $saidaDiesel . '</td>'
        . '<td>' . $retornoDiesel . '</td>'
        . '<td>' . $estoqueDieselFinal . '</td>'
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
                <td colspan="7">Vendas</td>
            </tr>
            <tr>
                <td>Bomba</td>
                <td>Bico</td>
                <td>Produto</td>
                <td>Saldo Inicial</td>
                <td>Saldo Final</td>
                <td>Quantidade #Litro</td>
                <td>Total</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

foreach ($modelsVendaGeral as $modelVendaGeral) {

    $table .= '<tr>'
            . '<td>' . $modelVendaGeral->bomba->descricao . '</td>'
            . '<td>' . $modelVendaGeral->descricao . '</td>'
            . '<td>' . $modelVendaGeral->tipoCombustivel->descricao . '</td>'
            . '<td>' . BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->orderBy(['registro.registro_id' => SORT_ASC])->one()->registro_anterior . '</td>'
            . '<td>' . BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->orderBy(['registro.registro_id' => SORT_DESC])->one()->registro_atual . '</td>'
            . '<td>' . BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->sum('((bico_registro.registro_atual - bico_registro.registro_anterior) - bico_registro.retorno)') . '</td>'
            . '<td>R$ ' . number_format(BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['bico_registro.bico_id' => $modelVendaGeral->bico_id, 'registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->sum('(((bico_registro.registro_atual - bico_registro.registro_anterior) - bico_registro.retorno) * bico_registro.valor)'), 2, ',', '.') . '</td>'
            . '</tr>';
}

$totalVendas = BicoRegistro::find()->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')->where(['registro.posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'registro.data', $model->data_inicial, $model->data_final])->sum('(((bico_registro.registro_atual - bico_registro.registro_anterior) - bico_registro.retorno) * bico_registro.valor)');

$table .= '<tr><td colspan="7">Total Vendas: R$ ' . number_format($totalVendas, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2">Vendas #Outras Receitas</td>
            </tr>
            <tr>
                <td>Categoria</td>
                <td>Quantidade #Litro</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

$table .= '<tr>'
            . '<td>Outros</td>'
            . '<td>' . $model->getVendaOutrosGeralQ() . '</td>';

$table .= '<tr><td colspan="2">Total Vendas #Outras Receitas: R$ ' . number_format($model->getVendaOutrosGeralT(), 2, ',', '.') . '</td></tr>';

$totalVendas += $model->getVendaOutrosGeralT();

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);


//Compras
$modelsCompraGeral = $model->getCompraGeral();
//$valorGasolina = $model->getValorCombustivel(1);
//$valorDiesel = $model->getValorCombustivel(2);

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="3">Compras</td>
            </tr>
            <tr>
                <td>Produto</td>
                <td>Quantidade</td>
                <td>Total</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

foreach ($modelsCompraGeral as $modelCompraGeral) {

    /*
    if ($modelCompraGeral->produto_id == 1)
        $table .= '<tr>'
                . '<td>' . $modelCompraGeral->descricao . ' #Estoque</td>'
                . '<td> ' . $estoqueGasolinaInicial . '</td>'
                . '<td>R$ ' . number_format($estoqueGasolinaInicial * $valorGasolina, 2, ',', '.') . '</td>'
                . '</tr>';

    if ($modelCompraGeral->produto_id == 2)
        $table .= '<tr>'
                . '<td>' . $modelCompraGeral->descricao . ' #Estoque</td>'
                . '<td> ' . $estoqueDieselInicial . '</td>'
                . '<td>R$ ' . number_format($estoqueDieselInicial * $valorDiesel, 2, ',', '.') . '</td>'
                . '</tr>';
     */

    $table .= '<tr>'
            . '<td>' . $modelCompraGeral->descricao . '</td>'
            . '<td>' . ProdutoNegociacao::find()->where(['negociacao_id' => 2, 'produto_id' => $modelCompraGeral->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'data', $model->data_inicial, $model->data_final])->sum('qtde') . '</td>'
            . '<td>R$ ' . number_format(ProdutoNegociacao::find()->where(['negociacao_id' => 2, 'produto_id' => $modelCompraGeral->produto_id, 'posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'data', $model->data_inicial, $model->data_final])->sum('qtde * valor'), 2, ',', '.') . '</td>'
            . '</tr>';
}

//$totalCompras += $estoqueGasolinaInicial * $valorGasolina;
//$totalCompras += $estoqueDieselInicial * $valorDiesel;
$totalCompras += ProdutoNegociacao::find()->where(['negociacao_id' => 2, 'posto_id' => Yii::$app->user->identity->posto_id])->andWhere(['between', 'data', $model->data_inicial, $model->data_final])->sum('qtde * valor');

$table .= '<tr><td colspan="3">Total Compras: R$ ' . number_format($totalCompras, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);










//Despesas
$modelsDespesaGeral = $model->getDespesaGeral();

$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2">Despesas</td>
            </tr>
            <tr>
                <td>Categoria</td>
                <td>Total</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

foreach ($modelsDespesaGeral as $modelDespesaGeral) {
    $totalTipoDespesa = Despesa::find()->where(['between', 'data_vencimento', $model->data_inicial, $model->data_final])->andWhere(['tipo_despesa_id' => $modelDespesaGeral->tipo_despesa_id])->sum('valor');
    $table .= '<tr>'
            . '<td>' . $modelDespesaGeral->descricao . '</td>'
            . '<td>R$ ' . number_format($totalTipoDespesa, 2, ',', '.') . '</td>'
            . '</tr>';

    $totalDespesa += $totalTipoDespesa;
}

$table .= '<tr><td colspan="2">Total Despesas   : R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td></tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);











//Geral
$table = '
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <td colspan="2">Resumo Geral</td>
            </tr>
            <tr>
                <td>Categoria</td>
                <td>Total</td>
            </tr>
        </thead>
';

$table .= '<tbody>';

$table .= '<tr>'
        . '<td>Total de Compras</td>'
        . '<td>R$ ' . number_format($totalCompras, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td>Total de Vendas</td>'
        . '<td>R$ ' . number_format($totalVendas, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td>Total de Despesas</td>'
        . '<td>R$ ' . number_format($totalDespesa, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td>Receita Bruta</td>'
            . '<td>R$ ' . number_format($totalVendas - $totalCompras, 2, ',', '.') . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td>Receita Líquida</td>'
        . '<td>R$ ' . number_format(($totalVendas - $totalCompras) - $totalDespesa, 2, ',', '.') . '</td>'
        . '</tr>';

$table .= '</tbody>';

$table .= '</table>';

$mpdf->WriteHTML($table);

$mpdf->Output();
exit;
