<?php

use yii\helpers\Url;
?>

<div class="row">
    
    <div class="col-md-4 col-xs-4 bgGray noPadding">
        <div class="col-md-12 col-xs-12 bgImagen" style="background-image: url('<?= $evaluador->usuario->byteImagen ?>');" ?>
            <div class="vertical-center text-center">
            </div>
        </div>
    </div>
    
    <div class="col-md-8 col-xs-8 bgGray">
            <div class="col-md-12 col-xs-12">
                <h3><strong>
                    <?= $evaluador->usuario->nombre; ?>
                    <?= $evaluador->usuario->appat; ?>
                    <?= $evaluador->usuario->apmat; ?>
                    </strong>
                </h3>
                <h4>
                <?php if( $evaluador->profesion ==! null):?>
                        <?= $evaluador->profesion; ?>
                <?php endif; ?>
                </h4>
                <p>&nbsp;</p>
            </div>

            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>RESEÑA</label></span>
                </div>
                <p>
                    <?= $evaluador->semblanza; ?>,
                </p>
                <br>
            </div>
            
            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>EXPERTISE</label></span>
                </div>
                <p>
                    <?php
                    $etiquetas = $evaluador->usuario->etiquetas;
                    if (count($etiquetas)) {
                        $strEtiquetas = '';
                        
                        foreach ($etiquetas as $etiqueta) {
                            $strEtiquetas .= $etiqueta->descripcion.', ';
                        }
                        
                        $strEtiquetas = substr($strEtiquetas, 0, (strlen($strEtiquetas)-2));
                        
                        echo $strEtiquetas;
                    }
                    ?>
                </p>
                <br>
            </div>
            
            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>EMPRESAS</label></span>
                </div>
                <p>
                    <?= $evaluador->universidad->nombre; ?>
                </p>
                <br>
            </div>

            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>SOCIAL</label></span>
                </div>
                <p>
                    <?php if( $evaluador->facebook ==! null): ?>
                    <a href="<?= $evaluador->facebook ?>" target='_blank'>
                        <img src="http://blog.addthiscdn.com/wp-content/uploads/2015/11/logo-facebook.png" style="width:35px"></img>
                    </a>
                    <?php endif ?>
                    <?php if( $evaluador->twitter ==! null):?>
                    <a href="<?= $evaluador->twitter ?>" target='_blank'>
                        <img src="http://journeylifecenter.org/wp-content/uploads/2013/12/one-twitter-png.png" style="width:35px"></img>
                    </a>
                    <?php endif?>
                </p>
                <br>
            </div>

    </div>
    
</div>
