<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Emprendedor */

$this->title = 'Registrar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emprendedor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'estados' => $estados,
        'ciudades' => $ciudades,
        'ciudadesNacimiento' => $ciudadesNacimiento,
        'universidades' => $universidades,
    ]) ?>

</div>
