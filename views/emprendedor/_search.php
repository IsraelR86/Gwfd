<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EmprendedorSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Opciones de búsqueda</h3>
    </div>
    
    <div class="panel-body">
        <div class="emprendedor-search">
        
            <?php $form = ActiveForm::begin([
                'id' => 'formEmprendedorSearch',
                'action' => ['index'],
                'method' => 'get',
            ]); ?>

            <?= $form->field($model, 'id_usuario') ?>

            <?= $form->field($model, 'fecha_nacimiento') ?>

            <?= $form->field($model, 'genero') ?>

            <?= $form->field($model, 'id_nivel_educativo') ?>

            <?= $form->field($model, 'universidad_otro') ?>

            <?php // echo $form->field($model, 'profesion') ?>

            <?php // echo $form->field($model, 'curp') ?>

            <?php // echo $form->field($model, 'rfc') ?>

            <?php // echo $form->field($model, 'tel_celular') ?>

            <?php // echo $form->field($model, 'tel_fijo') ?>

            <?php // echo $form->field($model, 'id_estado') ?>

            <?php // echo $form->field($model, 'id_ciudad') ?>

            <?php // echo $form->field($model, 'cp') ?>

            <?php // echo $form->field($model, 'direccion') ?>

            <?php // echo $form->field($model, 'estado_civil') ?>

            <?php // echo $form->field($model, 'colonia') ?>

            <?php // echo $form->field($model, 'id_estado_nacimiento') ?>

            <?php // echo $form->field($model, 'id_ciudad_nacimiento') ?>

            <?php // echo $form->field($model, 'id_universidad') ?>

            <?php // echo $form->field($model, 'facebook') ?>

            <?php // echo $form->field($model, 'twitter') ?>

            <?php // echo $form->field($model, 'pagina_web') ?>

            <div class="form-group">
                <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> Buscar', ['class' => 'btn btn-primary']) ?> &nbsp; 
                <?= Html::a('<span class="glyphicon glyphicon-list"></span> Listar todos', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        
            <?php ActiveForm::end(); ?>
        
        </div>
    </div>
</div>