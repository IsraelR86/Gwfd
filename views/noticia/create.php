<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Noticia */

$this->title = 'Registrar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="noticia-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'etiquetas' => $etiquetas,
    ]) ?>

</div>
