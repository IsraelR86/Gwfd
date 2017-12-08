<?php

use yii\helpers\Url;
?>

<div class="row">
    
    <div class="col-md-4 col-xs-4 bgGray noPadding">
        <div class="col-md-12 col-xs-12 bgImagen" <?php /*style="background-image: url('<?= $proyectos[0]->byteImagen ?>');"*/ ?>>
            <div class="vertical-center text-center">
                <div class="bgLogo img-circle">
                   <img src="<?= $institucion->usuario->byteimagen ?>" class="img-circle">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 col-xs-8 bgGray">
        <div class="col-md-12 col-xs-12">
            <h3 class="title2">
                <?= $institucion->nombre; ?>
            </h3>
        </div>
        <div class="col-md-6 col-xs-6 noPadding bgGray">
            <div class="col-md-12 col-xs-12">
                <?php if( $institucion->pagina_web ==! null):?>
                <div class="field-info">
                    <span class="container-label"><label>Página Web</label></span>
                </div>
                <p>
                    <?= $institucion->pagina_web ?>
                </p>
                <br>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-6 col-xs-6 noPaddingLeft ">
            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>Lugar de Residencia</label></span>
                </div>
                <p>
                    <?= $institucion->ciudad->descripcion; ?>,
                    <?= $institucion->ciudad->estado->descripcion; ?>
                </p>
                <br>
            </div>
        </div>
        
    </div>
    
</div>

<?php 
    $concursos = $institucion->concursos;
    foreach($concursos as $concurso):
?>

                <div class="col-md-4 col-xs-4 itemGanadores">
                    <div class="col-md-12 col-xs-12 noPadding">
                        <div class="col-md-12 col-xs-12 bgImagen proyectosGanadores" style="background-image: url('<?= $concurso->byteImagen ?>');" >
                            <img src="http://placehold.it/80x80" class="img-circle pull-right">
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 bgGray">
                        <div class="col-md-12 col-xs-8">
                            <br>
                            <p><strong><?= $concurso->nombre ?></strong></p>
                        </div>
                        
                    </div>
                </div>
<?php endforeach; ?>