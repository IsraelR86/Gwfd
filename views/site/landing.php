<?php
/* @var $this yii\web\View */
use app\models\Usuario;
use app\models\Concurso;
use yii\bootstrap\Modal;
use yii\helpers\Url;

//$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
//$this->registerJsFile(Url::to('@web/js/evaluador/concurso.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/landing.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);

$this->registerJs('var urlGetAllByEvaluador = "'.Url::toRoute('site/getultimosconcursos').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetAplicacion = "'.Url::toRoute('site/getbyid').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetRubricasByConcurso = "'.Url::toRoute('rubrica/getrubricas').'";', \yii\web\View::POS_HEAD);

?>
        <div class="row" id="news">
            <h2 class="center-block">¿ Qué hay de nuevo en la comunidad FWD ?</h2>
            <div class="row">
                
            </div>
        </div>
        <div class="row">
            <h2 class="center-block"> Concursos Recién Horneados </h2>
            <div class="row">
                <?php $concursos = Concurso::getAll(1,6);
                foreach( $concursos as $concurso): ?>
                <div class="item col-md-4" data-concurso="">
                    <div class="header">
                        <span class="title">{{nombre}}</span>
                            <a href="<?= Url::toRoute(['concurso/ganadores']) ?>?c={{id}}" title="Ganadores" class="icon iconGanadores tooltipster"><i class="fa fa-bullseye fa-lg icon"></i></a>
                        <img src="{{byteImagen}}">
                    </div>
                    <div class="body">
                        <span class="date">
                                    FECHA LIM: {{fechaCierre}}
                        </span>
                        <p>{{descripcion}}</p>
                    </div>
                    <div class="footer">
                        <p class="tag">
                            {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
                        </p>
                        <span class="icon">
                            <span class="btnModalConcurso" data-id="{{id}}">
                                <img src="<?= Url::to('@web/img/Edit.png'); ?>">
                            </span>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php 
       /* <div class="row">
            <?php 
            echo $this->renderFile('@app/views/templates/modal-info-concurso-eva-tpl.php');
            echo $this->renderFile('@app/views/templates/modal-info-rubricas-eva-tpl.php');
            echo $this->renderFile('@app/views/templates/waterfall-concursos-recientes-tpl.php');
            ?>
            <div class="waterfall" id="waterfall"></div>
        </div>
        ?>*/