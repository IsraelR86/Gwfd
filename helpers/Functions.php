<?php

namespace app\helpers;

use Yii;

class Functions
{
    /**
     * Convierte el array de los errores del model a una cadena de texto
     *
     * @param Array $arrayErrors Errores del modelo
     * @return String
     */
    public static function errorsToString($arrayErrors)
    {
        return array_reduce($arrayErrors, function($result, $item) { 
            return $result . (is_array($item) ? implode(', ', $item) : $item.'.') .' ';
        } );
    }
    
    /**
     * Convierte el array de los errores del model a una lista
     *
     * @param Array $arrayErrors Errores del modelo
     * @return String
     */
    public static function errorsToList($arrayErrors)
    {
        return array_reduce($arrayErrors, function($result, $item) { 
            return $result . (is_array($item) ? Functions::errorsToList($item) : '<li>'.$item.'</li>');
        } );
    }

    /**
     * Recibe una fecha en formato yyyy-mm-dd o dd-mm-yyyy y lo 
     * devuelve en el formato especificado por $formatOutput
     *
     * @param String $date
     * @param String $formatOutput Default Y-m-d
     */
    public static function transformDate($date, $formatOutput='Y-m-d')
    {
        if ($date == '' || $date == '0000-00-00' || $date == '00-00-0000') {
            return '';
        }
        
        $formatInput = '';
        $date_time = explode(' ', $date);
        $partsDate = explode('-', $date_time[0]);
        
        if (strlen($partsDate[0]) == 4) {
            $formatInput = 'Y-m-d';
        } else if (strlen($partsDate[0]) == 2) {
            $formatInput = 'd-m-Y';
        } else {
            return ''; // Formato no conocido
        }
        
        $fecha = date_create_from_format($formatInput, $date_time[0]);
        
        if ($fecha == false) {
            return '';
        }
        
        return date_format($fecha, $formatOutput);
        
        //Otra forma de hacerlo es con la clase Datetime
        /*$fecha = \DateTime::createFromFormat($formatInput, $date);
        return $fecha->format($formatOutput);*/
    }
    
    /**
     * Detecta si la accion es la que se esta ejecutando actualmente
     *
     * @param String $action
     * @return Boolean
     */
    public static function isCurrentAction($action)
    {
        return stripos(Yii::$app->request->getPathInfo(), $action) !== false ? true : false;
    }
    
    /**
     * Se convierte a un array de la forma [index => text] a [id => index, text => text]
     *
     * @param Array $array
     * @return Array
     */
    public static function arrayToObject($array, $keyIndex = 'id', $keyValue = 'text')
    {
        $result = [];
        
        if (is_array($array) && !empty($array)) {
            foreach ($array as $index => $value) {
                $result[] = [$keyIndex => $index, $keyValue => $value];
            }
        }
        
        return $result;
    }
    
    /**
     * Sube un archivo al servidor
     *
     * @param string $name Nombre del campo file del formulario
     * @param string $saveFile Nombre con el que se guardará el archivo
     * @return boolean true|false
     * @throws \Exception
     */
    public static function uploadFile($name, $saveFile, $getExtension = false)
    {
        if(isset($_FILES[$name]))
        {
            $error = $_FILES[$name]['error'];

            if (!empty($error)) {
                throw new \Exception('Error al subir la imagen. ' . Yii::$app->params['upload_error'][$error]);
            }

            if(!is_array($_FILES[$name]['name']))
            {
                $destino = Yii::$app->getBasePath().DIRECTORY_SEPARATOR.
                           Yii::$app->params['upload_dir'] . DIRECTORY_SEPARATOR;
                $ext = '';
                
                if ($getExtension) {
                    $ext = '.'.pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
                }
                
                $dirDestino = pathinfo($destino . $saveFile . $ext, PATHINFO_DIRNAME);
                
                if (!file_exists($dirDestino)) {
                    if (!mkdir($dirDestino)) {
                        throw new \Exception('No se puede crear el directorio ' . $dirDestino);
                    }
                }
                
                chmod($dirDestino, 0777);
                
                $uploadFile = move_uploaded_file($_FILES[$name]['tmp_name'], $destino . $saveFile . $ext);

                if ($_FILES[$name]['error']) {
                    throw new \Exception('Error al subir el archivo. ' . Yii::$app->params['upload_error'][$_FILES[$name]['error']]);
                }

                if (!is_writeable($destino . $saveFile . $ext)) {
                    throw new \Exception('No se puede escribir el archivo en el directorio destino. ' . $destino . $saveFile . $ext);
                }

                if (!$uploadFile) {
                    throw new \Exception('Error al subir el archivo. No se pudo copiar al directorio destino. ' . $destino . $saveFile);
                }

                return true;
            } else {
                return false;
            }
        }

        return false;
    }
    
    
    public static function compareDates($dateA, $dateB)
    {
        $objDateA = new \DateTime($dateA);
        $objDateB = new \DateTime($dateB);
        $days_diff = $objDateA->diff($objDateB);
        
        return intval($days_diff->format('%r%a'));
    }
}