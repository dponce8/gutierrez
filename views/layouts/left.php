<?php 
use app\controllers\SiteController;

?>
<aside class="main-sidebar">
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel" style="padding:10px 15px;">
            <div class="pull-left image">
                <img src="<?= \Yii::getAlias('@web/images/usr_img/'.Yii::$app->user->identity->img); ?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info" style="width: 180px; margin-top: -8px;">
                <p style="white-space: normal; line-height: 1.7;"><?= Yii::$app->user->identity->apellido.' '.Yii::$app->user->identity->nombre ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <?php 
        $menu = \app\models\User::getMenu(Yii::$app->user->identity->id);

        $info_menu = '<ul class="sidebar-menu tree" data-widget="tree">';
        $info_menu.= '
            <li>
                <a href="/index.php?r=site/index">
                    <i style="color: orange" class="fa fa-home"></i> <span>INICIO</span> 
                </a>
            </li>';

        $idPadre = 0;
        foreach ($menu as $m) {            
            if ($m['id_padre'] != $idPadre) {
                if ($idPadre == 0) {
                    $info_menu.= SiteController::creaPadre($m['padre'], $m['icono_padre']);
                } else {
                    $info_menu.= SiteController::cierraPadre();
                    $info_menu.= SiteController::creaPadre($m['padre'], $m['icono_padre']);
                }
                $info_menu.= SiteController::creaHijo($m['opcion'], $m['icono_hijo'], $m['url']);                            
            } else {
                $info_menu.= SiteController::creaHijo($m['opcion'], $m['icono_hijo'], $m['url']);
            }
            $idPadre = $m['id_padre'];    
        }

        $info_menu.= SiteController::cierraPadre();
        $info_menu.='</ul>';
        ?>

        <?= $info_menu; ?>       

    </section>
</aside>


