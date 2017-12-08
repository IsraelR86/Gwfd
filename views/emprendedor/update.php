<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Emprendedor */

$this->title = 'Actualizar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_usuario, 'url' => ['view', 'id' => $model->id_usuario]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="emprendedor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'estados' => $estados,
        'ciudades' => $ciudades,
        'ciudadesNacimiento' => $ciudadesNacimiento,
        'universidades' => $universidades,
    ]) ?>

</div>
