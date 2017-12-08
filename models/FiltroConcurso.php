<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filtros_concurso".
 *
 * @property integer $id
 * @property integer $id_concurso
 * @property integer $id_pregunta
 * @property integer $tipo_filtro
 * @property double $maximo
 * @property double $minimo
 * @property string $arreglo_opcion
 * @property string $comentarios
 * @property integer $validar_copia
 *
 * @property Concurso $concurso
 * @property Pregunta $pregunta
 * @property TipoFiltro $tipoFiltro
 */
class FiltroConcurso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filtros_concurso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arreglo_opcion', 'comentarios'], 'filter', 'filter' => 'strip_tags'],
            [['id_concurso', 'id_pregunta', 'tipo_filtro'], 'required'],
            [['id_concurso', 'id_pregunta', 'tipo_filtro', 'validar_copia'], 'integer'],
            [['maximo', 'minimo'], 'number'],
            [['arreglo_opcion'], 'string'],
            [['arreglo_opcion', 'comentarios'], 'app\validators\DelspacesValidator'],
            [['comentarios'], 'string', 'max' => 100],
            [['id_concurso', 'id_pregunta'], 'unique', 'targetAttribute' => ['id_concurso', 'id_pregunta'], 'message' => 'The combination of Id Concurso and Id Pregunta has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_concurso' => 'Concurso',
            'id_pregunta' => 'Pregunta',
            'tipo_filtro' => 'Tipo de Filtro',
            'maximo' => 'Máximo',
            'minimo' => 'Mínimo',
            'arreglo_opcion' => 'Opciones',
            'validar_copia' => 'Validar copias',
            'comentarios' => 'Comentarios',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcurso()
    {
        return $this->hasOne(Concurso::className(), ['id' => 'id_concurso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPregunta()
    {
        return $this->hasOne(Pregunta::className(), ['id' => 'id_pregunta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoFiltro()
    {
        return $this->hasOne(TipoFiltro::className(), ['id' => 'tipo_filtro']);
    }

    /**
     * Aplica el filtro a un proyecto y devuelve el resultado de la evaluación
     *
     * @param app\models\Proyecto
     * @return boolean Devuelve si aprovo o no el filtro
     */
    public function evaluar($proyecto)
    {
        $pasoFiltro = true;
        $respuesta = Respuesta::find()
            ->where('id_proyecto = '.$proyecto->id)
            ->andWhere('id_pregunta = '.$this->id_pregunta)
            ->one();

        // No respondió a la pregunta
        if ($respuesta == null) {
            return false;
        }

        switch ($this->tipo_filtro) {
            //----Texto

            case 1: // id=1: respuestas.respuesta_texto.length >= filtros_concurso.minimo (Mínimo de caracteres)
                $pasoFiltro = strlen($respuesta->respuesta_texto) >= $this->minimo;
            break;

            case 2: // id=2: respuestas.respuesta_texto.length <= filtros_concurso.maximo (Max de caracteres)
                $pasoFiltro = strlen($respuesta->respuesta_texto) <= $this->maximo;
            break;

            case 3: // id=3: Combinado de las 2 anteriores (Entre minimo y maximo)
                $pasoFiltro = strlen($respuesta->respuesta_texto) >= $this->minimo && strlen($respuesta->respuesta_texto) <= $this->maximo;
            break;

            // ----Numerico

            case 4: // id=4: respuestas.respuesta_numerica >= filtros_concurso.minimo
                $pasoFiltro = $respuesta->respuesta_numerica >= $this->minimo;
            break;

            case 5: // id=5: respuestas.respuesta_numerica <= filtros_concurso.maximo
                $pasoFiltro = $respuesta->respuesta_numerica <= $this->maximo;
            break;

            case 6: // id=6: Combinado de las 2 anteriores (Entre minimo y maximo)
                $pasoFiltro = $respuesta->respuesta_numerica >= $this->minimo && $respuesta->respuesta_numerica <= $this->maximo;
            break;

            case 7: // id=7: respuestas.respuesta_numerica = filtros_concurso.minimo
                $pasoFiltro = $respuesta->respuesta_numerica == $this->minimo;
            break;

            case 8: // id=8: respuestas.respuesta_numerica != filtros_concurso.minimo
                $pasoFiltro = $respuesta->respuesta_numerica != $this->minimo;
            break;

            // ----Opcion Unica

            case 9: // id=9: respuestas.respuesta_opcion = filtros_concurso.arreglo_opcion
                $pasoFiltro = $respuesta->respuesta_opcion == $this->arreglo_opcion;
            break;

            case 10: // id=10: respuestas.respuesta_opcion != filtros_concurso.arreglo_opcion
                $pasoFiltro = $respuesta->respuesta_opcion != $this->arreglo_opcion;
            break;

            // ----Opcion Multiple

            case 11: // id=11: "Seleccionada al menos una de: X, Y, ... Z"
                $arreglo_opcion = json_decode($this->arreglo_opcion);
                $respuesta_opcion = json_decode($respuesta->respuesta_opcion);
                $pasoFiltro = count(array_intersect($arreglo_opcion, $respuesta_opcion)) == 0 ? false : true;
            break;

            case 12: // id=12: "Seleccionadas todas de: X, Y, ... Z"
                // count(array_intersect(filtros_concurso.arreglo_opcion, respuestas.respuesta_opcion)) >= count(filtros_concurso.arreglo_opcion)
                $arreglo_opcion = json_decode($this->arreglo_opcion);
                $respuesta_opcion = json_decode($respuesta->respuesta_opcion);
                $pasoFiltro = count(array_intersect($arreglo_opcion, $respuesta_opcion)) == count($arreglo_opcion);
            break;

            case 13: // id=13: "Contiene solamente valores X, Y, ... Z"
                // count(array_intersect(filtros_concurso.arreglo_opcion, respuestas.respuesta_opcion)) == count(filtros_concurso.arreglo_opcion)
                $arreglo_opcion = json_decode($this->arreglo_opcion);
                $respuesta_opcion = json_decode($respuesta->respuesta_opcion);
                $pasoFiltro = count(array_diff($respuesta_opcion, $arreglo_opcion)) == 0;
            break;

            case 14: // id=14: "No contiene a los Valores X, Y, ... Z"
                // count(array_intersect(filtros_concurso.arreglo_opcion, respuestas.respuesta_opcion)) == 0
                $arreglo_opcion = json_decode($this->arreglo_opcion);
                $respuesta_opcion = json_decode($respuesta->respuesta_opcion);
                $pasoFiltro = count(array_intersect($arreglo_opcion, $respuesta_opcion)) == 0;
            break;

            default: // Tipo de Filtro no especificado
                $pasoFiltro = false;
        }

        return $pasoFiltro;

    }
}
