<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use app\helpers\MyHtml;
use app\models\Usuario;

if (Yii::$app->controller->id == 'institucion') {
    $this->registerJsFile(Url::to('@web/js/institucion/perfil.js'), ['depends' => [\app\assets\AppAsset::className()]]);
} else {
    $this->registerJsFile(Url::to('@web/js/perfil-usuario.js'), ['depends' => [\app\assets\AppAsset::className()]]);
}

$this->registerCssFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/uploadfile.css');
$this->registerJsFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/jquery.uploadfile.min.js', ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.maskedinput.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJs('var urlSetPerfil = "'.Url::toRoute('usuario/setperfil').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetPerfil = "'.Url::toRoute('usuario/getperfil').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetCiudades = "'.Url::toRoute('ciudad/getbyestado').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlChangePass = "'.Url::toRoute('usuario/changepass').'";', \yii\web\View::POS_HEAD);

?>
<div class="title_container">
    <h2 class="title">MI PERFIL</h2>
</div>

<div class="text-center" id="loadingMsg">
    <i class="fa fa-spinner fa-pulse fa-lg"></i>
</div>

<div>
<?php $form = ActiveForm::begin(['id' => 'frmPerfil']); ?>
    <div class="row">
        <div class="col-md-12 col-xs-12 text-center">
            <!--  <div class="avatar center-block" id="container-fileuploaderAvatar">  -->
            <div class="center-block" id="container-fileuploaderAvatar">
                <div id="fileuploaderAvatar" class="files">Avatar</div>
                
            </div>
          
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="email" class="tooltipster" placeholder="E-mail" name="email" title="E-mail" disabled />
            </div>
            
            <div class="field-input container-nombre">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Nombre" name="nombre" id="nombre" title="Nombre" />
            </div>
            <div class="error-nombre text-danger"></div>
            
            
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Apellido Paterno" name="appat" title="Apellido Paterno" />
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Apellido Materno" name="apmat" title="Apellido Materno" />
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Institución" name="extras[nombre]" title="Institución" />
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <textarea class="tooltipster" placeholder="Descripción" name="extras[descipcion]" title="Descripción" ></textarea>
            </div>
            
        </div>
        
        <div class="col-md-6">
            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_estado]" class="tooltipster" title="Estado">
                    <option value="">Estado</option>
                    <?php 
                    foreach($catalogos['estado'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_ciudad]" class="tooltipster" title="Ciudad">
                    <option value="">Ciudad</option>
                    <?php 
                    foreach($catalogos['ciudad_residencia'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Teléfono" name="extras[telefono]" title="Teléfono" />
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Facebook" name="extras[facebook]" title="Facebook" />
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Twitter" name="extras[twitter]" title="Twitter" />
            </div>
            
            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Sitio Web" name="extras[pagina_web]" title="Sitio Web" />
            </div>

        </div>
    </div>
    
    <div class="text-center">
    <a class="btnBorderRed inline-block fontBlack" href="#" id="btnGuardar">
        Guardar
    </a>
    <a class="btnBorderRed inline-block fontBlack" href="#" id="btnShowCambiarPass">
        Cambiar Contraseña
    </a>
    </div>
<?php ActiveForm::end(); ?>
</div>

<?php
Modal::begin([
    'id' => 'modalCambiarPass',
    'headerOptions' => ['class' => 'no-border'],
]);

    $form = ActiveForm::begin([
        'id' => 'frmCambiarPass',
        'action' => Url::toRoute('usuario/changepass'),
    ]);

    echo '<div class="title_container">
            <h3 class="title">Cambiar Contraseña</h3>
        </div>
        <div class="row">
            <div class="col-md-11 col-xs-11">
                <div class="field-input">
                    <span class="container-icon"></span>
                    <input type="password" class="tooltipster" placeholder="Nueva Contraseña" name="new_pass" id="new_pass" title="Nueva Contraseña" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-11 col-xs-11">
                <div class="field-input">
                    <span class="container-icon"></span>
                    <input type="password" class="tooltipster" placeholder="Confirmar Nueva Contraseña" name="new_pass_confirm" id="new_pass_confirm" title="Confirmar Nueva Contraseña" />
                </div>
            </div>
        </div>
        <div class="text-center">
            <a class="btnBorderRed inline-block fontBlack" href="#" id="btnSendCambiarPass">
                Guardar
            </a>
            <a class="btnBorderRed inline-block fontBlack" href="#modalCambiarPass" data-dismiss="modal">
                Cancelar
            </a>
        </div>';
        
    ActiveForm::end();

Modal::end();
?>