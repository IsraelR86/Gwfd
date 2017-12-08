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

<div class="row">
    <div class="col-md-6 center-block">
        <h3>¡Bienvenid@s!</h3>
        <p style="text-align:justify">
            Jóvenes emprendedores, a través de este espacio les damos la bienvenida al 7° Certamen Emprendedores 2017. Aquí podrán inscribir su proyecto y participar por uno de los tres lugares de la categoría única <b>"Proyectos de Emprendimiento"</b>, por lo que es importante cumplir con las bases, requisitos y progresos de registro que se establecen en la convocatoria.
        </p>
        <div class="text-right">
            Fecha límite de registro: 14 de septiembre de 2017
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 center-block">
        <h3>Iniciar Sesión</h3>
        
        <?php 
        if (Yii::$app->request->get('singup')) {
            echo '<div class="alert alert-success"><i class="fa fa-check"></i> Su registro ha sido exitoso, ahora puede iniciar sesión con los datos proporcionados.</div>';
        }
        ?>
        
        <?php $form = ActiveForm::begin(); ?>
                    
        	<?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
        
            <?= $form->field($model, 'username', [
                'enableLabel' => false,
                'inputTemplate' => '<span class="block input-icon input-icon-right"> {input} </span>',
                'inputOptions' => ['autofocus placeholder' => $model->getAttributeLabel('username')]
            ])->textInput() ?>
            
            <?= $form->field($model, 'password', [
                'enableLabel' => false,
                'inputTemplate' => '<span class="block input-icon input-icon-right"> {input} </span>',
                'inputOptions' => ['placeholder' => $model->getAttributeLabel('password')]
            ])->passwordInput() ?>
            
            <?php /*echo $form->field($model, 'captcha_code')->widget(Captcha::className(), [
                'imageOptions'=> [
                    'title' => 'Si es complicado leer el código, de click sobre la imagen para cambiar el código',
                    'data-toggle' => 'tooltip',
                ]
            ]);*/ ?>
            
            <div class="space"></div>
            
            <div class="text-right">
                <a data-toggle="modal" href='#modalRecuperarPass'>Olvidé mi contraseña</a>
                <br><br><a href="<?= URl::toRoute(['usuario/singup']) ?>" class="width-35 btn btn-default">
                    <i class="ace-icon fa fa-user-plus"></i>
                    <span class="bigger-120">¡Registrate!</span>
                </a>
                <button type="submit" class="width-35 btn btn-primary">
                    <i class="ace-icon fa fa-key"></i>
                    <span class="bigger-110">Ingresar</span>
                </button>
            </div>
            
            <div class="space-4"></div>
            
        <?php ActiveForm::end(); ?>
        
        <!--Login view, add the button:-->
        <?php 
        // echo yii\authclient\widgets\AuthChoice::widget([
        //      'baseAuthUrl' => ['site/auth']
        // ]); 
        ?>
        
    </div>
</div>


<?php 
Modal::begin([
    'id' => 'modalRecuperarPass',
    'header' => '<strong>Recuperar Contraseña</strong>',
]);

    $model = new RecuperarPassForm();

    echo '<div class="panel panel-default">
        <div class="panel-body">';?>

            <?php $form = ActiveForm::begin([
                'id' => 'formRecuperarPass',
                'layout' => 'horizontal',
                'action' => Url::toRoute(['usuario/recuperarpass']),
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-3',
                        'offset' => 'col-sm-offset-3',
                        'wrapper' => 'col-sm-8',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
            ]); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'captcha_code')->widget(Captcha::className(), [
                    'imageOptions'=> [
                        'title' => 'Si es complicado leer el código, de click sobre la imagen para cambiar el código',
                        'data-toggle' => 'tooltip',
                    ]
                ]); ?>

                <div class="form-group">
                    <div class="col-sm-8 col-sm-offset-3">
                        <button type="submit" class="btn btn-success" id="btnSendCambiarPass">Aceptar <i class="fa fa-exchange"></i></button> &nbsp;
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
                    </div>
                </div>

            <?php ActiveForm::end();
        echo '</div>
    </div>';

Modal::end();
?>