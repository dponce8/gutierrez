<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') { 
/**
 * Do not use this code in your template. Remove it. 
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">

    <head>
        <script src="<?= \Yii::getAlias('@web/js/jquery.min.js');?>"></script>
        <?php dmstr\web\AdminLteAsset::register($this); ?>       

        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title>Gutierrez Hermanos</title>
        <?php $this->head() ?>    

        <link href="<?= \Yii::getAlias('@web/css/switch.css'); ?>" rel="stylesheet" />
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/excellentexport@3.4.3/dist/excellentexport.min.js"></script>

        <?php
            $this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css");
            $this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js", ['position' => \yii\web\View::POS_HEAD]);
            $this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js", ['depends' => [\yii\web\JqueryAsset::className()]]);
            $this->registerJs('$(".my-chosen-select").chosen();');
        ?>
    </head>

    <body class="hold-transition <?= \dmstr\helpers\AdminLteHelper::skinClass() ?>  sidebar-mini">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>                 
    

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
