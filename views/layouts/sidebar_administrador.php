<?php
    use yii\bootstrap\Modal;
    use yii\helpers\Url;
    
    $view->registerJsFile(Url::to('@web/js/concurso/concursonuevo.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
    
    echo $this->renderFile('@app/views/templates/modal-frm-concurso-tpl.php');
    
    Modal::begin([
        'id' => 'modalConfirmConcursoNuevo',
        'headerOptions' => ['class' => 'no-border'],
    ]);
    
        echo '<div class="title_container">
                <h2 class="title">¡FELICIDADES!</h2>
            </div>
            <div clas="col-md-12 col-sm-12">
                <p class="text-center">Su concurso ha sido registrado exitosamente. <i class="fa fa-thumbs-o-up fa-2x"></i></p>
                <p class="text-center"><a class="btn btn-danger" data-dismiss="modal">Aceptar <i class="fa fa-check"></i></a></p>
            </div>';
    
    Modal::end();
    ?>
?>

<a href="#" class="btnBorderRed btnConcursoNuevo">
    <i class="fa fa-plus"></i> CONCURSO NUEVO
</a>
