<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\DespesaFixaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Despesa Fixas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="despesa-fixa-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Despesa Fixa', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'despesa_fixa_id',
            'posto_id',
            'tipo_despesa_id',
            'referencial',
            'valor',
            // 'observacao',
            // 'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
