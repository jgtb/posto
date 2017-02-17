<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\growl\Growl;
use edgardmessias\assets\nprogress\NProgressAsset;

AppAsset::register($this);
NProgressAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => !Yii::$app->user->isGuest ? Yii::$app->user->identity->getPosto() : 'My Company',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Início', 'visible' => !Yii::$app->user->isGuest, 'url' => ['site/index']],
                    //['label' => 'Responsáveis', 'visible' => Yii::$app->user->identity->tipo_usuario_id == 1, 'url' => ['/usuario', 'id' => 2], 'options' => ['class' => Yii::$app->controller->id == 'usuario' && $_GET['id'] == 2 ? 'active' : '']],
                    ['label' => 'Registro', 'visible' => !Yii::$app->user->isGuest, 'items' => [
                            ['label' => 'Bombas & Bicos', 'visible' => !Yii::$app->user->isGuest, 'url' => ['/bomba'], 'options' => ['class' => Yii::$app->controller->id == 'bomba' ? 'active' : '']],
                            ['label' => 'Registros', 'url' => ['/registro'], 'options' => ['class' => Yii::$app->controller->id == 'registro' ? 'active' : '']],
                        ]],
                    ['label' => 'Produto', 'visible' => !Yii::$app->user->isGuest, 'items' => [
                            //['label' => 'Produtos', 'url' => ['/produto'], 'options' => ['class' => Yii::$app->controller->id == 'produto' && Yii::$app->controller->action->id != 'estoque' ? 'active' : '']],
                            ['label' => 'Compras', 'url' => ['/produto-negociacao', 'id' => 2], 'options' => ['class' => Yii::$app->controller->id == 'produto-negociacao' && $_GET['id'] == 2 ? 'active' : '']],
                            ['label' => 'Vendas', 'url' => ['/produto-negociacao', 'id' => 1], 'options' => ['class' => Yii::$app->controller->id == 'produto-negociacao' && $_GET['id'] == 1 ? 'active' : '']],
                            //['label' => 'Vendas', 'url' => ['/produto-negociacao', 'id' => 1], 'options' => ['class' => Yii::$app->controller->id == 'produto-negociacao' && $_GET['id'] == 1 ? 'active' : '']],
                            ['label' => 'Estoque', 'url' => ['/produto/estoque']],
                        ]],
                    ['label' => 'Despesa', 'visible' => !Yii::$app->user->isGuest, 'items' => [
                            ['label' => 'Categorias', 'url' => ['/tipo-despesa'], 'options' => ['class' => Yii::$app->controller->id == 'tipo-despesa' ? 'active' : '']],
                            ['label' => 'Despesas', 'url' => ['/despesa', 'id' => 1], 'options' => ['class' => Yii::$app->controller->id == 'despesa' && $_GET['id'] == 1 ? 'active' : '']],
                        ]],
                    ['label' => 'Login', 'visible' => Yii::$app->user->isGuest, 'url' => ['/site/login']],
                    ['label' => Yii::$app->user->identity->email, 'visible' => !Yii::$app->user->isGuest, 'items' => [
                            ['label' => 'Meu Perfil', 'url' => ['/usuario/view', 'id' => Yii::$app->user->identity->usuario_id]],
                            //['label' => 'Meus Postos', 'visible' => Yii::$app->user->identity->tipo_usuario_id == 1, 'url' => ['/site/meus-postos']],
                            ['label' => 'Relatórios', 'visible' => Yii::$app->user->identity->tipo_usuario_id == 1, 'url' => ['/relatorio/create']],
                            ['label' => 'Manutenção', 'visible' => Yii::$app->user->identity->status == 2, 'url' => ['/valor-combustivel/update', 'id' => Yii::$app->user->identity->posto_id]],
                            '<li class="divider"></li>',
                            ['label' => 'Logout',
                                'url' => ['/site/logout'],
                                'linkOptions' => ['data-method' => 'post']
                            ],
                        ]],
                ],
            ]);
            NavBar::end();
            ?>

            <div class="container">
                <?=
                Breadcrumbs::widget([
                    'homeLink' => ['label' => Yii::$app->user->isGuest ? 'My Company' : 'Página Principal', 'url' => ['/site/index']],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
                    <?=
                    Growl::widget([
                        'type' => $type,
                        'icon' => false,
                        'showSeparator' => false,
                        'body' => $message['body'],
                        'delay' => 500,
                        'pluginOptions' => [
                            'showProgressbar' => true,
                            'placement' => [
                                'from' => 'top',
                                'align' => 'right',
                            ]
                        ]
                    ]);
                    ?>
                <?php endforeach; ?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

                <p class="pull-right">Powered by Yii Framework</p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
