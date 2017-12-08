<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Noticia */

$this->title = 'Datos de la ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="noticia-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> Ir al listado', ['index'], ['class' => 'btn btn-default']) ?> &nbsp;
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?> &nbsp;
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Esta seguro que desea eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-12 text-center">
            <img src="<?= $model->bytePortada ?>" class="portada_noticia">
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'table table-striped table-bordered table-hover detail-view'
        ],
        'attributes' => [
            'titulo',
            'fecha:datetime',
            'autor',
            'activo:boolean',
            'resumen:html',
            'contenido:html',
            [
                'attribute' => 'etiquetas',
                'value' => $model->getStr_etiquetas(),
            ]
        ],
    ]) ?>

</div>
