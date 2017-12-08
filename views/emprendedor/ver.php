<?php

?>

<div class="row">
    
    <div class="col-md-4 col-xs-4 bgGray noPadding">
        <div class="col-md-12 col-xs-12 bgImagen" style="background-image: url('<?= $emprendedor->usuario->getByteimagen() ?>');">
            
        </div>
    </div>
    
    <div class="col-md-8 col-xs-8 bgGray">
        <div class="col-md-12 col-xs-12">
            <h3 class="title2">
                <?= $emprendedor->usuario->nombre; ?>
                <?= $emprendedor->usuario->appat; ?>
                <?= $emprendedor->usuario->apmat; ?>
            </h3>
        </div>
        <div class="col-md-6 col-xs-6 noPadding bgGray">
            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>CORREO</label></span>
                </div>
                <p>
                    <?= $emprendedor->usuario->email ?>
                </p>
                <br>
                <?php if( $emprendedor->universidad_otro ==! null):?>
                <div class="field-info">
                    <span class="container-label"><label>UNIVERSIDAD</label></span>
                </div>
                <p>
                    <?= $emprendedor->universidad_otro ?>
                </p>
                <br>
                <?php endif; ?>
                <?php if( $emprendedor->profesion ==! null):?>
                <div class="field-info">
                    <span class="container-label"><label>Profesión</label></span>
                </div>
                <p>
                    <?= $emprendedor->profesion ?>
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
                    <?= $emprendedor->ciudadNacimiento->descripcion; ?>,
                    <?= $emprendedor->ciudadNacimiento->estado->descripcion; ?>
                </p>
                <br>
                <?php if( $emprendedor->tel_celular ==! null ): ?>
                <div class="field-info">
                    <span class="container-label"><label>Celular</label></span>
                </div>
                <p>
                    <?= $emprendedor->tel_celular; ?>
                </p>
                <br>
                <?php endif; ?>
                <?php if( $emprendedor->tel_fijo ==! null ): ?>
                <div class="field-info">
                    <span class="container-label"><label>Teléfono fijo</label></span>
                </div>
                <p>
                    <?= $emprendedor->tel_fijo; ?>
                </p>
                <br>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
</div>

<?php $proyectos = $emprendedor->emprendedoresXProyectos; ?>
<?php if(!empty($proyectos)): ?>
    <h2 class="title2">Proyectos</h2>
    <div class="row">
    <?php foreach($proyectos as $proyecto): ?>
        <div class="col-md-4 col-xs-4 itemGanadores">
            <div class="col-md-12 col-xs-12 noPadding">
                <div class="col-md-12 col-xs-12 bgImagen proyectosGanadores" style="background-image: url('<?= $proyecto->proyecto->byteImagen ?>');" >
                    <img src="http://placehold.it/80x80" class="img-circle pull-right">
                </div>
            </div>
            <div class="col-md-12 col-xs-12 bgGray">
                <div class="col-md-12 col-xs-8">
                    <br>
                    <p><strong><?= $proyecto->proyecto->nombre ?></strong></p>
                    <?php 
                        if( $proyecto->proyecto->ganadores != null )
                        {
                            foreach( $proyecto->proyecto->ganadores as $concurso)
                            {
                                $ganadores[] = $concurso;
                            }
                        }
                    ?>
                </div>
                
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
    </div>

<br>

<?php if(!empty($ganadores)): ?>
    <h2 class="title2">Premios</h2>
    <div class="row">
    <?php foreach($ganadores as $proyectoGanador): ?>
            <div class="col-md-4 itemGanadores">
                <div class="col-md-12 col-xs-12 noPadding">
                    <div class="col-md-12 col-xs-12 bgImagen proyectosGanadores" style="background-image: url('<?= $proyectoGanador->idConcurso->byteImagen ?>');" >
                        <img src="http://placehold.it/80x80" class="img-circle pull-right">
                    </div>
                </div>
                <div class="col-md-12 col-xs-12 bgGray">
                    <div class="col-md-12 col-xs-8">
                        <br>
                        <p><strong><?= $proyectoGanador->idConcurso->nombre ?></strong></p>
                    </div>
                    
                </div>
            </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>