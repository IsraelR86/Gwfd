<?php
    use yii\helpers\Url;
    use app\models\Usuario;
    use yii\bootstrap\Nav;
?>
<div class="row" id="row-header-title">
    <div class="col-md-1 col-xs-1 container-menu-toggle">
        <?= (yii::$app->controller->action->id !== 'error' && Yii::$app->user->identity != null) ? '
            <div id="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
                <div>MENU</div>
            </div>' : '' ?>
    </div>
    <div class="col-md-2 col-xs-2">
        <!-- space -->
        &nbsp;
    </div>
    <div class="col-md-5 col-xs-4">
        <a href="<?= Url::home() ?>">
            <img src="<?php
                if (Yii::$app->user->identity != null) {
                    switch(Yii::$app->user->identity->tipo) {
                        case Usuario::$EMPRENDEDOR:
                            echo Url::to('@web/img/FWD_Talent.png');
                            break;

                        case Usuario::$ADMINISTRADOR:
                        case Usuario::$INSTITUCION:
                            echo Url::to('@web/img/FWD_Business.png');
                            break;

                        default:
                            echo Url::to('@web/img/FWD.png');
                    }
                } else {
                    echo Url::to('@web/img/FWD.png');
                }

            ?>" width="139"></img>
        </a>
    </div>
    <div class="col-md-5 col-xs-4">
            <img src="<?php
                if (Yii::$app->user->identity != null) {
                    switch(Yii::$app->user->identity->tipo) {
                      case Usuario::$EMPRENDEDOR:
                      case Usuario::$EVALUADOR:
                      case Usuario::$INSTITUCION:
                            echo Url::to('@web/img/Logo-Certamen.png');
                            break;
                    }
                }
                else {
                    echo Url::to('@web/img/Logo-Certamen.png');
                }
            ?>" width="139"></img>
    </div>
    <div class="col-md-4 col-xs-4">
            <img src="<?php
                if (Yii::$app->user->identity != null) {
                    switch(Yii::$app->user->identity->tipo) {
                        case Usuario::$EMPRENDEDOR:
                        case Usuario::$EVALUADOR:
                        case Usuario::$INSTITUCION:
                            echo Url::to('@web/img/FESE-logo.png');
                            break;
                    }
                }
                else {
                    echo Url::to('@web/img/FESE-logo.png');
                }
            ?>" width="139"></img>
    </div>
    <div class="col-md-5 col-xs-5 text-center">
        <?php
        if (yii::$app->controller->action->id !== 'error' && Yii::$app->user->identity != null) { ?>
            <!--<span class="icon icon_header" id="icon_search">
                <img src="<?= Url::to('@web/img/bot_busca.png'); ?>"></img>
            </span>
            <span id="seccion_search">
                <input type="text" name="input_search" id="input_search" style="display: none;"/>
            </span>-->
            <a class="icon icon_header" href="<?= Url::toRoute(yii::$app->controller->id.'/perfil') ?>">
                <img src="<?= Url::to('@web/img/bot_mi_info.png'); ?>"></img>
            </a>
            <a class="icon icon_header" href="<?= Url::toRoute(['/site/logout']) ?>" data-method='post'>
                <img src="<?= Url::to('@web/img/bot_salir.png'); ?>"></img>
            </a>
        <?php } ?>
    </div>
</div>
