<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NoticiaSearch */
/* @var $form yii\bootstrap\ActiveForm */

$this->registerJsFile(Url::to('@web/js/plugins/jquery.maskedinput.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/tinymce/tinymce.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJs("
    $('.datepicker').mask('99-99-9999', {placeholder:'dd-mm-yyyy'});
");
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Opciones de búsqueda</h3>
    </div>

    <div class="panel-body">
        <div class="noticia-search">

            <?php $form = ActiveForm::begin([
                'id' => 'formNoticiaSearch',
                'action' => ['index'],
                'method' => 'get',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}",
                    'horizontalCssClasses' => [
                        'label' => 'col-md-3',
                        'wrapper' => 'col-md-9',
                    ],
                    'options' => [
                         // La clase del div que contiene al input, por defecto es form-group
                         // Pero se agrega col-md-6 para formar dos columnas de inputs
                        'class' => 'form-group col-md-6',
                    ],
                ],
            ]); ?>

            <?= $form->field($model, 'titulo') ?>

            <?= $form->field($model, 'fecha', [
                'inputOptions' => [
                    'class' => 'datepicker form-control',
                ]]) ?>

            <?= $form->field($model, 'autor') ?>

            <?php echo $form->field($model, 'activo')->dropDownList([0=>'No', 1=>'Si'], ['prompt' => 'Seleccione el Status']); ?>

            <div class="form-group">
                <div class="col-md-12 text-center">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> Buscar', ['class' => 'btn btn-danger']) ?> &nbsp;
                    <?= Html::a('<span class="glyphicon glyphicon-list"></span> Listar todos', ['index'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>