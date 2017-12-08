<?php
use app\helpers\MyHtml;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model app\models\Usuario */
/* @var $form yii\bootstrap\ActiveForm */

?>
<div class="container">
    <div class="row">
        
        <div class="col-md-6 center-block">
                <h3><?= ($model->isNewRecord ? 'Regístrate' : 'Editar perfil') ?></h3>
            <?php
            if (Yii::$app->session->hasFlash('result')) {
                echo MyHtml::alert(Yii::$app->session->getFlash('result')['message'], Yii::$app->session->getFlash('result'));
            }
            ?>
            
            <div id="slick">
        
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'formUsuario',
                        'method' => 'post'
                    ]); 
                    $objHtml = new MyHtml();
                    $objHtml->setForm($form);
                    $objHtml->setModel($model);
                ?>
                <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
                
                <?= $objHtml->input('email', ['icon' => 'entypo-user'], ['maxlength' => 100]); ?>
                
                <?= $objHtml->input('password', ['icon' => 'entypo-user'], ['maxlength' => 10]); ?>
                
                <?= $objHtml->input('password_repeat', ['icon' => 'entypo-user'], ['maxlength' => 10]); ?>
                
                <?= $objHtml->input('nombre', ['icon' => 'entypo-user'], ['maxlength' => 45]); ?>
                
                <?= $objHtml->input('appat', ['icon' => 'entypo-user'], ['maxlength' => 45]); ?>
                
                <?= $objHtml->input('apmat', ['icon' => 'entypo-user'], ['maxlength' => 45]); ?>
                
                <?php
                echo $form->field($model, 'captcha_code', [ 'options' => [
                    'class' => 'field'], // La clase del div que contiene al input, por defecto es form-group
                ])->widget(Captcha::className(), [
                    'options' => [
                    	'placeholder' => $model->getAttributeLabel('captcha_code'), 
                    	'class' => '' , // La clase del input, por defecto es form-control
                    ],
                    'template' => '{image} <div class="field"> {input}'.
                                       '<span class="entypo-user icon"></span> </div>'.
                                       '',
                    'imageOptions'=> [
                        'title' => 'Si es complicado leer el código, de click sobre la imagen para cambiar el código',
                        'data-toggle' => 'tooltip',
                    ]
                ])->label(false);
                ?>
        
                <div class="form-group" style="height:2em">
                    <?= Html::submitInput(($model->isNewRecord ? 'Guardar' : 'Actualizar'), ['class' => 'btn ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary'),'style' => 'height:inherit;width:5em']) ?>&nbsp; 
                    <?= Html::a('<button type="button" class="btn btn-danger" style="height:2em;width:5em">Cancelar</button>', ['index']) ?>
                </div>
            
                <?php ActiveForm::end(); ?>
            
            </div>
        
        </div>
    </div>
</div>