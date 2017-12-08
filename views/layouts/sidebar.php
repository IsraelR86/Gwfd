<?php
/* @var $view \yii\web\View */

use app\models\Usuario;
use app\models\Notificacion;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$view->registerCssFile(Url::to('@web/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css', ['depends' => [\yii\bootstrap\BootstrapAsset::className()]]));
$view->registerJsFile(Url::to('@web/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js'), ['depends' => [\yii\bootstrap\BootstrapPluginAsset::className()]]);
$view->registerJsFile(Url::to('@web/js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js'), ['depends' => [\yii\bootstrap\BootstrapPluginAsset::className()]]);
$view->registerJsFile(Url::to('@web/js/sidebar.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('var urlImg = "'.Url::to('@web/img').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var fechasCalendario = '.json_encode(Notificacion::getFechasCalendario()).';', \yii\web\View::POS_HEAD);
$this->registerJs('var urlToogleWidgetSidebar = "'.Url::toRoute(['site/tooglewidgetsidebar']).'";', \yii\web\View::POS_HEAD);

// Los modales deben de estar fuera de sibebar, porque al estar collapsado el sidebar los modales no se muestran
switch(Yii::$app->user->identity->tipo) {
    case Usuario::$EMPRENDEDOR:
            Modal::begin([
                'id' => 'modalFrmProyecto',
                'size' => Modal::SIZE_LARGE,
                'headerOptions' => ['class' => 'no-border'],
                'clientOptions' => ['backdrop' => 'static'],
            ]);

            Modal::end();
    case Usuario::$EVALUADOR:
            /*Modal::begin([
                'id' => 'modalMapa',
                'header' => 'Seleccione los puntos de referencias',
            ]);

                echo '<div class="item">';
                echo '<div id="mapa"></div>';
                echo '<div class="col-md-12 text-center footer"><a class="btnBorderRed btnFinalizarMapa" href="" data-id="">Finalizar</a></div>';
                echo '</div>';

            Modal::end();*/

            echo '<div id="dialogMapaPuntoRadial" class="dialogMapaPuntoRadial" style="display:none" title="Seleccione las referencias geográficas">';
                echo '<div style="font-size:small">Referencias Geográficas</div>';
                echo '<div id="mapaPuntoRadial" class="mapa"></div>';
                echo '<div class="col-md-12 text-center footer"><a class="btnBorderRed fontBlack btnFinalizarMapa" href="" data-id="">Finalizar</a></div>';
            echo '</div>';

            echo '<div id="dialogMapaPoligono" style="display:none" title="Seleccione las referencias geográficas">';
                echo '<div style="font-size:small">Referencias Geográficas</div>';
                echo '<div id="mapaPoligono" class="mapa"></div>';
                echo '<div class="col-md-12 text-center footer"><a class="btnBorderRed fontBlack btnFinalizarMapa" href="" data-id="">Finalizar</a>
                    <a class="btnBorderRed fontBlack btnLimpiarMapa" href="" data-id="">Limpiar Mapa</a></div>';
            echo '</div>';

            echo '<div id="dialogMapaPunto" style="display:none" title="Seleccione las referencias geográficas">';
                echo '<div style="font-size:small">Referencias Geográficas</div>';
                echo '<div id="mapaPunto" class="mapa"></div>';
                echo '<div class="col-md-12 text-center footer"><a class="btnBorderRed fontBlack btnFinalizarMapa" href="" data-id="">Finalizar</a></div>';
            echo '</div>';
        break;

    case Usuario::$ADMINISTRADOR:
    case Usuario::$INSTITUCION:
            Modal::begin([
                'id' => 'modalFrmConcurso',
                'size' => Modal::SIZE_LARGE,
                'headerOptions' => ['class' => 'no-border'],
                'clientOptions' => ['backdrop' => 'static'],
            ]);

            Modal::end();

            echo '<div id="dialogTipoPreguntaConcurso" style="display:none" title="Seleccione el tipo de pregunta que desea agregar">';
                echo '<form>
                        <div class="radio">
                            <label> <input type="radio" name="tipoPregunta" value="1"> Texto</label>
                        </div>
                        <div class="radio">
                            <label> <input type="radio" name="tipoPregunta" value="2"> Archivo</label>
                        </div>
                     </form>';
            echo '</div>';
        break;
}
?>

<div class="col-md-3 col-xs-3 collapse" id="sidebar" style="display: none;">
    <div>

    <div id="calendario"></div>

    <?php
    switch(Yii::$app->user->identity->tipo) {
        case Usuario::$EMPRENDEDOR:
            echo $view->renderFile('@app/views/layouts/sidebar_emprendedor.php', ['view' => $view]);
            break;

        case Usuario::$ADMINISTRADOR:
        case Usuario::$INSTITUCION:
            echo $view->renderFile('@app/views/layouts/sidebar_administrador.php', ['view' => $view]);
            break;

        default:
            echo 'Opciones no disponibles';
    }
    ?>

    <hr />

    <div class="widget_sidebar <?= Yii::$app->session->get('notificaciones') !== null ? (Yii::$app->session->get('notificaciones') ? 'active' : '') : 'active' ?>" id="notificaciones">
        <div class="header">
            <span class="icon">
                <img src="<?= Url::to('@web/img/desplegar.png'); ?>"></img>
            </span>
            NOTIFICACIONES
        </div>
        <div class="body nano" <?= Yii::$app->session->get('notificaciones') !== null ? (Yii::$app->session->get('notificaciones') ? '' : 'style="display: none;"') : '' ?>>
            <ul class="nano-content">
                <?php
                    $notificaciones = [];

                    switch(Yii::$app->user->identity->tipo) {
                        case Usuario::$EMPRENDEDOR:
                            $notificaciones = Notificacion::getNotificacionesEvaluador(Yii::$app->user->identity->id);
                            break;

                        case Usuario::$ADMINISTRADOR:
                            break;

                        case Usuario::$INSTITUCION:
                            break;
                    }

                    if (count($notificaciones)) {
                        foreach ($notificaciones as $notificacion) {
                            echo '<li data-id="'.$notificacion['id'].'">
                                <p>'.$notificacion['subtitulo'].' - '.$notificacion['titulo'].'</p>
                                <span class="icon tooltipster"
                                title="<div>
                                    <span class=\'title\'>'.$notificacion['titulo'].'<br>'.$notificacion['subtitulo'].'</span><hr>
                                    <p>'.$notificacion['contenido'].'</p>
                                    </div>">
                                </span>
                            </li>';
                        }
                    }
                ?>
            </ul>
        </div>
    </div>

    <div class="widget_sidebar <?= Yii::$app->session->get('badges') !== null ? (Yii::$app->session->get('badges') ? 'active' : '') : 'active' ?>" id="badges">
        <div class="header">
            <span class="icon">
                <img src="<?= Url::to('@web/img/desplegar.png'); ?>"></img>
            </span>
            BADGES
        </div>
        <div class="body" <?= Yii::$app->session->get('badges') !== null ? (Yii::$app->session->get('badges') ? '' : 'style="display: none;"') : '' ?>>
            <?php
            $badges = Yii::$app->user->identity->badgesXUsuario;
            if (count($badges)) {
                foreach ($badges as $badge) {
                    echo '<img src="'.Url::to('@web/img/badges/'.$badge->id_badge.'.png').'" title="<strong>'.$badge->badge->descripcion.'</strong>: '.$badge->badge->nota.'" class="badged tooltipster" />';
                }
            }
            ?>
        </div>
    </div>

    <div class="widget_sidebar <?= Yii::$app->session->get('concursos') !== null ? (Yii::$app->session->get('concursos') ? 'active' : '') : 'active' ?>" id="concursos">
        <div class="header">
            <span class="icon">
                <img src="<?= Url::to('@web/img/desplegar.png'); ?>"></img>
            </span>
            CONCURSOS ACTIVOS
        </div>
        <div class="body nano" <?= Yii::$app->session->get('concursos') !== null ? (Yii::$app->session->get('concursos') ? '' : 'style="display: none;"') : '' ?>>
            <ul class="nano-content">
                <?php
                    $concursosActivos = Notificacion::getConcursosActivos();

                    if (count($concursosActivos)) {
                        foreach ($concursosActivos as $concurso) {
                            echo '<li data-id="'.$concurso['id'].'">
                                <p>'.$concurso['titulo'].'</p>
                                <span class="icon tooltipster"
                                title="<div>
                                    <span class=\'title\'>'.$concurso['titulo'].'<br>CONCURSO ACTIVO</span><hr>
                                    <p>'.$concurso['contenido'].'</p>
                                    </div>">
                                </span>
                            </li>';
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
    </div>
</div>