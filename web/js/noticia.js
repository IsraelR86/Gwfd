/* globals $ */
'use strict';

$(document).ready(function() {
    tinymce.init({
        selector: '.tinymce',
        language: 'es_MX',
        plugins: [
            'advlist autolink lists link image charmap preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'paste textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: 'preview media | forecolor backcolor',
        image_advtab: true,
    });

    $('.tinymce').parents('form').on('beforeValidate', function() {
        tinymce.triggerSave();
    });

     $('[name="Noticia[etiquetas][]"]').select2({
        placeholder: 'Seleccione una o más etiquetas',
        language: 'es'
    });
});