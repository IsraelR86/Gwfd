<?php 
/* @var $view \yii\web\View */

use yii\helpers\Url;
use app\helpers\MyHtml;
use app\helpers\Functions;
?>

<ul class="menu">
    <li>
        <a href="<?= Url::toRoute('evaluador/concursos'); ?>" class="<?= MyHtml::classActualPage('evaluador/concursos'); ?>">CONCURSOS</a>
    </li>
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('evaluador/misevaluaciones'); ?>" class="<?= MyHtml::classActualPage('evaluador/misevaluaciones'); ?>">MIS EVALUACIONES</a>
        <?php 
         //if (!Functions::isCurrentAction('evaluador/misevaluaciones')) {
             $countProyectosPendientes = count(Yii::$app->user->identity->evaluador->proyectosPendientes());
             
             if ($countProyectosPendientes > 0) {
                echo '<span class="badge">'.$countProyectosPendientes.'</span>';
             }
         //}
        ?>
    </li>
    <li class="separator"></li>
    <li>
        <a href="<?= Url::toRoute('evaluador/ver'); ?>" class="<?= MyHtml::classActualPage('evaluador/ver'); ?>">MI PERFIL</a>
    </li>
</ul>