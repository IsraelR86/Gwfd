<?php
    use yii\bootstrap\Modal;
    use yii\helpers\Url;

    $view->registerJsFile(Url::to('@web/js/emprendedor/proyectonuevo.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
    $view->registerJsFile(Url::to('//maps.google.com/maps/api/js?key=AIzaSyA7R0-dIqqboUBfhemO-b2Knqbb0OnR9Io&language=es'), ['depends' => [\yii\web\JqueryAsset::className()]]);
    $view->registerJsFile(Url::to('@web/js/plugins/gmaps.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
    $view->registerJsFile(Url::to('@web/js/plugins/gmap3/gmap3.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
    $view->registerJsFile(Url::to('@web/js/plugins/gmap3/gmap3-menu.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
    $view->registerJsFile(Url::to('@web/js/emprendedor/pregunta_geografica.js?rand='.rand()), ['depends' => [\yii\web\JqueryAsset::className()]]);

    $view->registerCssFile(Url::to('@web/js/plugins/gmap3/gmap3-menu.css', ['depends' => [\yii\bootstrap\BootstrapAsset::className()]]));

    echo $this->renderFile('@app/views/templates/modal-frm-proyecto-tpl.php');

    Modal::begin([
        'id' => 'modalConfirmProyectoNuevo',
        'headerOptions' => ['class' => 'no-border'],
    ]);

        echo '<div class="title_container">
                <h2 class="title">¡FELICIDADES!</h2>
            </div>
            <div clas="col-md-12 col-sm-12">
                <p class="text-center">Su proyecto ha sido registrado exitosamente. <i class="fa fa-thumbs-o-up fa-2x"></i></p>
                <p class="text-center"><a class="btn btn-danger" data-dismiss="modal">Aceptar <i class="fa fa-check"></i></a></p>
            </div>';

    Modal::end();
    ?>
?>

<a href="#" class="btnBorderRed btnProyectoNuevo">
    <i class="fa fa-plus"></i> PROYECTO NUEVO
</a>

