<?php
// Variables de configuración global

return [
    'adminEmail' => 'admin@example.com',
    'title' => 'goForward',
    'mail_host' => 'smtp.1and1.mx',
    'mail_username' => 'info@gofwd.mx',
    'mail_password' => 'G0fwd2016',
    'mail_port' => '587', // 465, 587
    'mail_encryption' => 'tls', // ssl, tls
    'upload_dir' => 'uploads', // Directorio donde se guardarán los archivos subidos, relativo al directorio raíz de todo el proyecto
    'public_host' => 'http://inmx-desarrolladoraplicacion.c9.io/web/', // Dirección http pública para el sitio
    'max_etiquetas_proyectos' => 5, // Numero máximo de etiquetas
    'tipos_usuarios' => [
        1 => 'Administrador',
        2 => 'Evaluador',
        //3 => 'Jurídico',
        //4 => 'Promoción',
        //5 => 'Emprendedor',
        //6 => 'Integrante',
    ],
    'nivel_educativo' => [
        /*1 => 'Primaria',
        2 => 'Secundaria',
        3 => 'Bachillerato',*/
        4 => 'Licenciatura',
     // 5 => 'Maestría',
     // 6 => 'Doctorado',
    ],
    'estado_civil' => [
        1 => 'Soltero(a)',
        2 => 'Casado(a)',
        3 => 'Divorciado(a)',
        4 => 'Unión Libre',
        5 => 'Viudo',
    ],
    'genero' => [
        1 => 'Femenino',
        2 => 'Masculino',
    ],
    'upload_error' => [
        UPLOAD_ERR_OK         => "No errors.",
        UPLOAD_ERR_INI_SIZE   => "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE  => "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL    => "Partial upload.",
        UPLOAD_ERR_NO_FILE    => "No file.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION  => "File upload stopped by extension.",
    ]
];
