<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Posto;

$this->title = $model->nome;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuario-view">

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel-meus-postos">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center text-uppercase"><?= Html::encode($this->title) ?></h1>
                    </div>
                    <div class="panel-body">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                ['attribute' => 'tipo_usuario_id', 'value' => $model->tipoUsuario->descricao_singular],
                                ['attribute' => 'posto_id', 'visible' => Yii::$app->user->identity->tipo_usuario_id != 1, 'value' => Posto::findOne(['posto_id' => $model->posto_id])->descricao],
                                'nome',
                                ['attribute' => 'email', 'label' => 'Login / E-mail', 'value' => $model->email],
                            ],
                        ])
                        ?>
                    </div>
                    <div class="panel-footer text-center">
                        <p>
                            <?= Html::a('Alterar Perfil', ['update', 'id' => $model->usuario_id], ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Alterar Senha', ['update-senha', 'id' => $model->usuario_id], ['class' => 'btn btn-warning']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
