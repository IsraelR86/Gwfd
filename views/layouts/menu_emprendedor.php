<?php 
/* @var $view \yii\web\View */

use yii\helpers\Url;
use app\helpers\MyHtml;
?>

<ul class="menu">
    <li>
        <a href="<?= Url::toRoute('emprendedor/misproyectos'); ?>" class="<? MyHtml::classActualPage('emprendedor/misproyectos'); ?>">MIS PROYECTOS</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="#" class="btnProyectoNuevo"><i class="fa fa-plus"></i> NUEVO PROYECTO</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('emprendedor/aplica'); ?>" class="<?= MyHtml::classActualPage('emprendedor/aplica'); ?>">¡APLICA!</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('emprendedor/misconcursos'); ?>" class="<?= MyHtml::classActualPage('emprendedor/misconcursos'); ?>">MIS CONCURSOS</a>
    </li>
</ul>