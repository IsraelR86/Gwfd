<?php 
/* @var $view \yii\web\View */

use yii\helpers\Url;
use app\helpers\MyHtml;
?>

<ul class="menu">
    <li>
        <a href="<?= Url::toRoute('institucion/concursos'); ?>" class="<?= MyHtml::classActualPage('institucion/concursos'); ?>">CONCURSOS</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('institucion/evaluaciones'); ?>" class="<?= MyHtml::classActualPage('institucion/evaluaciones'); ?>">EVALUACIONES</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('institucion/evaluadores'); ?>" class="<?= MyHtml::classActualPage('institucion/evaluadores'); ?>">EVALUADORES</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="#" class="btnConcursoNuevo"><i class="fa fa-plus"></i> NUEVO CONCURSO</a>
    </li>
</ul>