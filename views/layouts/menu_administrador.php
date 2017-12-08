<?php
/* @var $view \yii\web\View */

use yii\helpers\Url;
use app\helpers\MyHtml;
?>

<ul class="menu">
    <li>
        <a href="<?= Url::toRoute('administrador/concursos'); ?>" class="<?= MyHtml::classActualPage('administrador/concursos'); ?>">CONCURSOS</a>
    </li>
    <!-- <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('administrador/evaluaciones'); ?>" class="<?= MyHtml::classActualPage('administrador/evaluaciones'); ?>">EVALUACIONES</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('administrador/ganadores'); ?>" class="<?= MyHtml::classActualPage('administrador/ganadores'); ?>">GANADORES</a>
    </li> -->
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('noticia/index'); ?>" class="<?= MyHtml::classActualPage('noticia'); ?>">NOTICIAS</a>
    </li>
</ul>