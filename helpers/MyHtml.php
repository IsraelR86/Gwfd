<?php

namespace app\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class MyHtml
{
    public $_form = null;
    public $_model = null;
    
    /**
     * Convierte el array de los errores del model a una cadena de texto
     *
     * @param string $message
     * @param array $params alert-class, alert-icon, alert-close
     * @return string
     */
    public static function alert($message, $params = [])
    {
        if (!isset($params['alert-close'])) {
            $params['alert-close'] = true;
        }

        return '<div class="alert alert-block '.(isset($params['alert-class']) ? $params['alert-class'] : 'alert-success').'">'.
            ($params['alert-close'] ? '<button type="button" class="close" data-dismiss="alert">
                <i class="ace-icon fa fa-times"></i>
            </button>' : '').
            '<i class="ace-icon fa fa-'.(isset($params['alert-icon']) ? $params['alert-icon'] : 'check green').'"></i>&nbsp;
            '.$message.'
        </div>';
    }

    /**
     * Convierte una matriz de datos a una tabla html
     *
     * @param array $data Arreglo bidimensional de datos
     * @param array $headers Array asociativo con las etiquetas de los encabezados $key => $label
     * @param array $firstColum Array asociativo con las etiquetas de la primera columna $key => $label
     * @param array $skipColumns Array asociativo con las llaves de las columnas que se quieran omitir al momento de imprimir
     * @param array $italics Array con palabras o valores que se deben convertir en italicas
     * @param string $classCss Clase CSS para la tabla
     *
     * @return string Html Table
     */
    public static function arrayToTable($data, $headers = [], $firstColum = [], $skipColumns = [], $italics = [], $classCss = 'table-striped table-bordered table-hover')
    {
        $tableHtml = '<div class="table-responsive">';
        $tableHtml .= '<table class="table '.$classCss.'">';
        $tbody = '<tr><td colspan="text-center">No se encontraron datos</td></tr>';

        if (!empty($data)) {
            if ($headers !== false) {
                $keysHeader = array_keys(current($data));
                if (empty($headers)) {
                    $headers = $keysHeader;
                }

                if (count($headers)) {
                    $tableHtml .= '<thead><tr>';

                    foreach($keysHeader as $labelHeader) {
                        if (!in_array($labelHeader, $skipColumns)) {
                            $tableHtml .= '<th>'.(isset($headers[$labelHeader]) ? $headers[$labelHeader] : $labelHeader).'</th>';
                        }
                    }

                    $tableHtml .= '</tr></thead>';
                }
            }

            $tbody = '';

            foreach ($data as $keyFila => $fila) {
                $tbody .= '<tr>';

                if (!empty($firstColum)) {
                    // No se utiliza array_shift porque se pierde la referenia del index/key
                    // y es necesario para $skipColumns
                    $firstCelda = current($fila);
                    // Por eso se utiliza next, para avanzar al siguiente elemento del array
                    next($fila);

                    $tbody .= '<td>'.(isset($firstColum[$firstCelda]) ? $firstColum[$firstCelda] : $firstCelda).'</td>';
                }

                // No se utiliza foreach porque cuando inicia su ejecución,
                // el puntero interno del array se pone automáticamente en el primer elemento
                // y queremos saltar el primero
                while (list($keyCelda, $celda) = each($fila)) {
                    if (!in_array($keyCelda, $skipColumns)) { // Celdas que se deben de saltar
                        if (in_array($celda, $italics)) { // Celdas que se deben de convertir en italicas
                            $celda = '<span class="not-set">'.$celda.'</span>';
                        }

                        $tbody .= '<td>'.$celda.'</td>';
                    }
                }

                $tbody .= '</tr>';
            }
        }

        $tableHtml .= '<tbody>';
        $tableHtml .= $tbody;
        $tableHtml .= '</tbody>';
        $tableHtml .= '</table>';
        $tableHtml .= '</div>';

        return $tableHtml;
    }
    
    public function setForm($form) {
        $this->_form = $form;
    }
    
    public function setModel($model) {
        $this->_model = $model;
    }
    
    /**
     * @param string $attr   Nombre del atributo/campo del modelo
     * @param array  $config Arreglo asociativo con los valores de configuración
     * @param string $type   Tipo de elemento a mostrar
     * @param array  $extras Arreglo asociativo con los valores extras de configuración necesarios para algunos elementos
     * 
     * @return string Html Input
     */
    public function input($attr, $config, $extras = [], $type = 'textInput') {
        $default = [
            //'enableLabel' => false, // Otra forma de dehabilitar los labels 'showLabels'=>false
            'options' => [
            	'class' => 'field' // La clase del div que contiene al input, por defecto es form-group
            ],
            'inputOptions' => [
            	'placeholder' => $this->_model->getAttributeLabel($attr), 
            	'class' => '' , // La clase del input, por defecto es form-control
            	//'required' => $this->_model->isAttributeRequired($attr)
        	],
            'inputTemplate' => '{input}'.
                               '<span class="'.(isset($config['icon']) ? $config['icon'] : '').' icon"></span>'.
                               '<span class="slick-tip left">'.$this->_model->getAttributeLabel($attr).'</span>',
        ];
        
        return $this->_form
            ->field($this->_model, $attr, $default)
            ->$type($extras)->label(false);
    }
    
    /**
     * @param string $attr   Nombre del atributo/campo del modelo
     * @param array  $config Arreglo asociativo con los valores de configuración
     * @param string $type   Tipo de elemento a mostrar
     * @param array  $extras Arreglo asociativo con los valores extras de configuración necesarios para algunos elementos
     * 
     * @return string Html Input
     */
    public function inputSelect($attr, $config, $items = []) {
        $default = [
            //'enableLabel' => false, // Otra forma de dehabilitar los labels 'showLabels'=>false
            'options' => [
            	'class' => 'field' // La clase del div que contiene al input, por defecto es form-group
            ],
            'inputOptions' => [
            	'placeholder' => $this->_model->getAttributeLabel($attr), 
            	'class' => '' , // La clase del input, por defecto es form-control
            	'required' => $this->_model->isAttributeRequired($attr)
        	],
            'inputTemplate' => '{input}'.
            					'<div id="arrow-select"></div>'.
                        		'<svg id="arrow-select-svg"></svg>'.
                               '<span class="'.(isset($config['icon']) ? $config['icon'] : '').' icon"></span>'.
                               '<span class="slick-tip left">'.$this->_model->getAttributeLabel($attr).'</span>',
        ];
        
        return $this->_form
            ->field($this->_model, $attr, $default)
            ->dropdownList($items, ['prompt' => $this->_model->getAttributeLabel($attr)])->label(false);
    }
    
    /**
     * Detecta si la accion es la que se esta ejecutando actualmente
     * si es asi, devuelve la clase active
     *
     * @return Boolean
     */
    public static function classActualPage($page)
    {
        //$page = Yii::$app->controller->id.'/'.yii::$app->controller->action->id;
        //$page = Yii::$app->urlManager->parseRequest(Yii::$app->request); // Devuelve un Array
        
        return Functions::isCurrentAction($page) ? 'active' : '';
    }
    
    /**
     * Crea el HTML del paginador
     * 
     * @param string $className Default "wide"
     * @param string $txtPrev Default "Anterior"
     * @param string $txtNext Default "Siguiente"
     *
     * @return String HTML
     */
    public static function pager($className = 'wide', $txtPrev = 'Anterior', $txtNext = 'Siguiente') 
    {
        return '<div class="pager '.$className.'">
            <div class="prev">
                <a href="#" class="btnPrev"><span class="icon"><img src="'.Url::to('@web/img/Back.png').'"></span> '.$txtPrev.'</a>
            </div>
            
            <div class="next">
                <a href="#" class="btnNext">'.$txtNext.' <span class="icon"><img src="'.Url::to('@web/img/Next.png').'"></span></a>
            </div>
        </div>';
    }

}