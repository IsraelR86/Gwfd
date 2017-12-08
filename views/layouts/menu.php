<?php 
use app\models\Usuario;
/* @var $view \yii\web\View */
?>
<nav class="row" id="row-header-menu">
    <div class="col-md-3 col-xs-1">
        <!-- space -->
    </div>
    <div class="col-md-9 col-xs-11">
    <?php 
    switch(Yii::$app->user->identity->tipo) {
        case Usuario::$EMPRENDEDOR:
            echo $view->renderFile('@app/views/layouts/menu_emprendedor.php', ['view' => $view]);
            break;
        
        case Usuario::$ADMINISTRADOR:
            echo $view->renderFile('@app/views/layouts/menu_administrador.php', ['view' => $view]);
            break;
        
        case Usuario::$INSTITUCION:
            echo $view->renderFile('@app/views/layouts/menu_institucion.php', ['view' => $view]);
            break;
        case Usuario::$EVALUADOR:
            echo $view->renderFile('@app/views/layouts/menu_evaluador.php', ['view' => $view]);
            break;
        default:
            echo 'Opciones no disponibles';
    }
    ?>
    </div>
</nav>