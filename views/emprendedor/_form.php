<?php
use app\helpers\MyHtml;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Emprendedor */
/* @var $form yii\bootstrap\ActiveForm */

$this->registerJsFile(Url::to('@web/js/emprendedor/form.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.maskedinput.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="emprendedor-form">
    
    <div id="slick">

        <?php 
            $form = ActiveForm::begin(['id' => 'formEmprendedor']); 
            $objHtml = new MyHtml();
            $objHtml->setForm($form);
            $objHtml->setModel($model);
        ?>
        
        <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
    
        <?= $objHtml->input('fecha_nacimiento', ['icon' => 'entypo-user']); ?>
    
        <?= $objHtml->inputSelect('genero', ['icon' => 'entypo-user'], Yii::$app->params['genero']); ?>

        <?= $objHtml->inputSelect('id_nivel_educativo', ['icon' => 'entypo-user'], Yii::$app->params['nivel_educativo']); ?>

        <?= $objHtml->input('universidad_otro', ['icon' => 'entypo-user'], ['maxlength' => 50]); ?>

        <?= $objHtml->input('profesion', ['icon' => 'entypo-user'], ['maxlength' => 50]); ?>

        <?= $objHtml->input('curp', ['icon' => 'entypo-user'], ['maxlength' => 18]); ?>

        <?= $objHtml->input('rfc', ['icon' => 'entypo-user'], ['maxlength' => 15]); ?>

        <?= $objHtml->input('tel_celular', ['icon' => 'entypo-user'], ['maxlength' => 15]); ?>

        <?= $objHtml->input('tel_fijo', ['icon' => 'entypo-user'], ['maxlength' => 15]); ?>

        <?= $objHtml->inputSelect('id_estado', ['icon' => 'entypo-user'], $estados); ?>

        <?= $objHtml->inputSelect('id_ciudad', ['icon' => 'entypo-user'], $ciudades); ?>

        <?= $objHtml->input('cp', ['icon' => 'entypo-user'], ['maxlength' => 6]); ?>

        <?= $objHtml->input('direccion', ['icon' => 'entypo-user'], ['maxlength' => 45]); ?>

        <?= $objHtml->inputSelect('estado_civil', ['icon' => 'entypo-user'], Yii::$app->params['estado_civil']); ?>

        <?= $objHtml->input('colonia', ['icon' => 'entypo-user'], ['maxlength' => 50]); ?>

        <?= $objHtml->inputSelect('id_estado_nacimiento', ['icon' => 'entypo-user'], $estados); ?>

        <?= $objHtml->inputSelect('id_ciudad_nacimiento', ['icon' => 'entypo-user'], $ciudadesNacimiento); ?>

        <?= $objHtml->inputSelect('id_universidad', ['icon' => 'entypo-user'], $universidades); ?>

        <?= $objHtml->input('facebook', ['icon' => 'entypo-user'], ['maxlength' => 45]); ?>

        <?= $objHtml->input('twitter', ['icon' => 'entypo-user'], ['maxlength' => 45]); ?>

        <?= $objHtml->input('pagina_web', ['icon' => 'entypo-user'], ['maxlength' => 100]); ?>

        <div class="form-group">
            <?= Html::submitInput(($model->isNewRecord ? 'Guardar' : 'Actualizar'), ['class' => 'btn ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]) ?> &nbsp; 
            <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> Cancelar', ['index'], ['class' => 'send']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

</div>
