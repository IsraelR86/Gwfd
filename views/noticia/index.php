<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NoticiaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titulo_plu;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="noticia-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus-sign"></span> Registrar', ['create'], ['class' => 'btnBorderRed fontBlack']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-hover',
        ],
        'layout' => "{summary}\n<div class='table-responsive'>\n{items}\n</div>\n{pager}",
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'titulo',
            'fecha:datetime',
            'autor',
            'resumen',
            'activo:boolean',

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
