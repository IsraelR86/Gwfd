<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use app\helpers\MyHtml;
use app\models\Usuario;

$this->registerJsFile(Url::to('@web/js/perfil-usuario.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerCssFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/uploadfile.css');
$this->registerJsFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/jquery.uploadfile.min.js', ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.maskedinput.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJs('var urlGetPerfil = "'.Url::toRoute('usuario/getperfil').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSetPerfil = "'.Url::toRoute('usuario/setperfil').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetCiudades = "'.Url::toRoute('ciudad/getbyestado').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlChangePass = "'.Url::toRoute('usuario/changepass').'";', \yii\web\View::POS_HEAD);

if (Yii::$app->session->hasFlash('alert')) {
    $this->registerJs("$.jAlert({
		'title': 'Completa tu Perfil',
		'content': '<div class=\"text-justify\">".Yii::$app->session->getFlash('alert')."</div>',
		'theme': 'red'
	});");
}
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
                <input type="text" class="tooltipster" placeholder="Fecha de nacimiento" name="extras[fecha_nacimiento]" title="Fecha de Nacimiento" />
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_estado]" class="tooltipster" title="Estado de Residencia">
                    <option value="">Estado de Residencia</option>
                    <?php
                    foreach($catalogos['estado'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_ciudad]" class="tooltipster" title="Ciudad de Residencia">
                    <option value="">Ciudad de Residencia</option>
                    <?php
                    foreach($catalogos['ciudad_residencia'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[genero]" class="tooltipster" title="Género">
                    <option value="">Género</option>
                    <?php
                    foreach($catalogos['genero'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[estado_civil]" class="tooltipster" title="Estado Civil">
                    <option value="">Estado Civil</option>
                    <?php
                    foreach($catalogos['estado_civil'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_nivel_educativo]" class="tooltipster" title="Nivel Educativo">
                    <option value="">Nivel Educativo</option>
                    <?php
                    foreach($catalogos['nivel_educativo'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_universidad]" class="tooltipster" title="Universidad">
                    <option value="">Universidad</option>
                    <?php
                    foreach($catalogos['universidad'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['nombre'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Otra Universidad" name="extras[universidad_otro]" title="Otra Universidad" />
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Profesión" name="extras[profesion]" title="Profesión" />
            </div>

            <!--<div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="CURP" name="extras[curp]" title="CURP"/>
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="RFC" name="extras[rfc]" title="RFC" />
            </div>-->

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Celular" name="extras[tel_celular]" title="Celular" />
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Teléfono" name="extras[tel_fijo]" title="Teléfono" />
            </div>
            <!--
            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_estado_nacimiento]" class="tooltipster" title="Estado de Nacimiento">
                    <option value="">Estado de Nacimiento</option>
                    <?php
                    foreach($catalogos['estado'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <select name="extras[id_ciudad_nacimiento]" class="tooltipster" title="Ciudad de Nacimiento">
                    <option value="">Ciudad de Nacimiento</option>
                    <?php
                    foreach($catalogos['ciudad_nacimiento'] as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['descripcion'].'</option>';
                    }
                    ?>
                </select>
            </div>-->

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Dirección" name="extras[direccion]" title="Dirección" />
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Colonia" name="extras[colonia]" title="Colonia" />
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="C.P." name="extras[cp]"  title="C.P." maxlength="5"/>
            </div>

            <!--<div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Facebook" name="extras[facebook]" title="Facebook" />
            </div>

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Twitter" name="extras[twitter]" title="Twitter" />
            </div>-->

            <div class="field-input">
                <span class="container-icon"></span>
                <input type="text" class="tooltipster" placeholder="Sitio Web" name="extras[pagina_web]" title="Sitio Web" />
            </div>

            <?php if (Yii::$app->user->identity->tipo == Usuario::$EVALUADOR): ?>
            <div class="field-input">
                <span class="container-icon"></span>
                <textarea class="tooltipster" placeholder="Semblanza" name="extras[semblanza]" title="Semblanza" ></textarea>
            </div>
            <?php endif; ?>
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
