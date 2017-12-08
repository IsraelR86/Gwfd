<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EmprendedorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titulo_plu;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emprendedor-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus-sign"></span> Registrar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-hover',
        ],
        'layout' => "{summary}\n<div class='table-responsive'>\n{items}\n</div>\n{pager}",
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_usuario',
            'fecha_nacimiento',
            'genero',
            'id_nivel_educativo',
            'universidad_otro',
            // 'profesion',
            // 'curp',
            // 'rfc',
            // 'tel_celular',
            // 'tel_fijo',
            // 'id_estado',
            // 'id_ciudad',
            // 'cp',
            // 'direccion',
            // 'estado_civil',
            // 'colonia',
            // 'id_estado_nacimiento',
            // 'id_ciudad_nacimiento',
            // 'id_universidad',
            // 'facebook',
            // 'twitter',
            // 'pagina_web',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-center action-buttons"> {view} &nbsp; {update} &nbsp; {delete} </div>',
                'buttons' => [
                    'view'  => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                        ]);
                    },
                    'update'  => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                        ]);
                    },
                    'delete'  => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', '¿Esta seguro que desea eliminar este registro?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                        ]);
                    }
                ]
            ]
        ],
    ]); ?>

</div>
