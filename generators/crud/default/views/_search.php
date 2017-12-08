<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->searchModelClass, '\\') ?> */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Opciones de búsqueda</h3>
    </div>
    
    <div class="panel-body">
        <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">
        
            <?= "<?php " ?>$form = ActiveForm::begin([
                'id' => 'form<?= (StringHelper::basename($generator->modelClass)) ?>Search',
                'action' => ['index'],
                'method' => 'get',
            ]); ?>

<?php
        $count = 0;
        foreach ($generator->getColumnNames() as $attribute) {
            if (++$count < 6) {
                echo "            <?= " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
            } else {
                echo "            <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
            }
        }
        ?>
            <div class="form-group">
                <?= "<?= " ?>Html::submitButton('<span class="glyphicon glyphicon-search"></span> Buscar', ['class' => 'btn btn-primary']) ?> &nbsp; 
                <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-list"></span> Listar todos', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        
            <?= "<?php " ?>ActiveForm::end(); ?>
        
        </div>
    </div>
</div>