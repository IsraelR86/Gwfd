<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Emprendedor */

$this->title = 'Datos de ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emprendedor-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> Ir al listado', ['index'], ['class' => 'btn btn-default']) ?> &nbsp; 
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Actualizar', ['update', 'id' => $model->id_usuario], ['class' => 'btn btn-primary']) ?> &nbsp; 
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_usuario], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Esta seguro que desea eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'table table-striped table-bordered table-hover detail-view'
        ],
        'attributes' => [
            'id_usuario',
            'fecha_nacimiento',
            'genero',
            'id_nivel_educativo',
            'universidad_otro',
            'profesion',
            'curp',
            'rfc',
            'tel_celular',
            'tel_fijo',
            'id_estado',
            'id_ciudad',
            'cp',
            'direccion',
            'estado_civil',
            'colonia',
            'id_estado_nacimiento',
            'id_ciudad_nacimiento',
            'id_universidad',
            'facebook',
            'twitter',
            'pagina_web',
        ],
    ]) ?>

</div>
