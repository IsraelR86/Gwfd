<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

$this->registerJs('
    $(document).ready(function(){
        $("#generator-modelclass").on("change", function(){
            var input = $(this).val();
            var name = input.split("\\\");
            
            $("#generator-searchmodelclass").val("app\\\models\\\"+name[2]+"Search");
            $("#generator-controllerclass").val("app\\\controllers\\\"+name[2]+"Controller");
        });
    });
');

echo $form->field($generator, 'modelClass')->textInput(['placeholder' => 'app\models\\']);
echo $form->field($generator, 'searchModelClass')->textInput(['placeholder' => 'app\models\Search']);
echo $form->field($generator, 'controllerClass')->textInput(['placeholder' => 'app\controllers\Controller']);
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
