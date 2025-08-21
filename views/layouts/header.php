<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">
    <?= Html::a('<span class="logo-mini">Gutierrez</span><span class="logo-lg">Gutierrez</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation" style="height: 51px; padding-top: 0px;">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="height: 40px">
                    <span class="sr-only">Toggle navigation</span>
                </a>        
            </li>     
        </ul>  
        <div class="navbar-custom-menu navbar-static-right">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= \Yii::getAlias('@web/images/usr_img/'.Yii::$app->user->identity->img); ?>" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= Yii::$app->user->identity->nombre.' '.Yii::$app->user->identity->apellido ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= \Yii::getAlias('@web/images/usr_img/'.Yii::$app->user->identity->img); ?>" class="img-circle" alt="User Image"/>

                            <p>
                            <?= Yii::$app->user->identity->nombre.' '.Yii::$app->user->identity->apellido ?>
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                            <a href="index.php?r=user%2Fview&id=<?=Yii::$app->user->identity->id?>" class="btn btn-default btn-flat">Perfil</a>
                            </div>
                            <div class="pull-left" style="padding-left: 25px">
                                <a href="index.php?r=site/cambio-pass" class="btn btn-success btn-flat">Contraseña</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Salir',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-danger btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div> 
    </nav>
</header>
