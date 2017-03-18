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
                'brandLabel' => 'Carro Tanque',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Início', 'url' => ['site/index']],
                    ['label' => 'Alugueis', 'url' => ['/caminhao-cliente'], 'options' => ['class' => Yii::$app->controller->id == 'caminhao-cliente' ? 'active' : '']],
                    ['label' => 'Despesa', 'visible' => !Yii::$app->user->isGuest, 'items' => [
                            ['label' => 'Categorias', 'url' => ['/tipo-despesa'], 'options' => ['class' => Yii::$app->controller->id == 'tipo-despesa' ? 'active' : '']],
                            //['label' => 'Despesas Fixas', 'url' => ['/despesa-fixa', 'id' => 2], 'options' => ['class' => Yii::$app->controller->id == 'despesa-fixa' && $_GET['id'] == 2 ? 'active' : '']],
                            ['label' => 'Despesas', 'url' => ['/despesa', 'id' => 2], 'options' => ['class' => Yii::$app->controller->id == 'despesa' && $_GET['id'] == 2 ? 'active' : '']],
                        ]],
                    ['label' => 'Relatórios', 'visible' => Yii::$app->user->identity->tipo_usuario_id == 1, 'url' => ['/relatorio/create']],
                    ['label' => Yii::$app->user->identity->email, 'visible' => !Yii::$app->user->isGuest, 'items' => [
                            ['label' => 'Meu Perfil', 'url' => ['/usuario/view', 'id' => Yii::$app->user->identity->usuario_id]],
                            ['label' => 'Sair',
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
                    'homeLink' => ['label' => Yii::$app->user->isGuest ? 'My Company' : 'Início', 'url' => ['/site/index']],
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
                <p class="pull-left">&copy; Postos Kleuter</p>

                <p class="pull-right"><?= date('Y') ?></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
