<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\helpers\MyHtml;
use yii\helpers\Url;
use yii\authclient\widgets\AuthChoice;
use app\models\RecuperarPassForm;
use yii\bootstrap\Modal;

$this->registerJsFile(Url::to('@web/js/usuario/recuperarpass.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJs('var urlRecuperarPass ="'.Url::toRoute(['usuario/recuperarpass']).'";', \yii\web\View::POS_HEAD);

$this->registerJs("$('body').css({
    'background': 'url(/web/img/fondo.jpg)',
});");
?>

<div class="row center-block">
    <div class="col-md-4 col-md-offset-4">
        
            <h3>Recupera tu contraseña</h3>
            <?php $form = ActiveForm::begin([
                'id' => 'formRecuperarPass',
                'action' => Url::toRoute(['usuario/recuperarpass']),
            ]); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'captcha_code')->widget(Captcha::className(), [
                    'imageOptions'=> [
                        'title' => 'Si es complicado leer el código, de click sobre la imagen para cambiar el código',
                        'data-toggle' => 'tooltip',
                    ]
                ]); ?>


            <div class="text-right">
                <button type="submit" class="width-35 btn btn-success">
                    <span class="bigger-110">Aceptar</span>
                </button>
            </div>
            <?php ActiveForm::end(); ?>
    </div>
</div>