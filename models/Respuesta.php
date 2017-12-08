<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "respuestas".
 *
 * @property integer $id
 * @property integer $id_proyecto
 * @property integer $id_pregunta
 * @property double $respuesta_numerica
 * @property string $respuesta_texto
 * @property string $respuesta_opcion
 * @property string $respuesta_geografica
 * @property double $ponderacion
 * @property string $fecha_edicion
 *
 * @property Pregunta $pregunta
 * @property Proyecto $proyecto
 */
class Respuesta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'respuestas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['respuesta_texto', 'respuesta_opcion', 'respuesta_geografica'], 'filter', 'filter' => 'strip_tags'],
            [['respuesta_texto', 'respuesta_opcion', 'respuesta_geografica'], 'app\validators\DelspacesValidator'],
            [['id_proyecto', 'id_pregunta', 'fecha_edicion'], 'required'],
            [['id_proyecto', 'id_pregunta'], 'integer'],
            [['respuesta_numerica', 'ponderacion'], 'number'],
            [['respuesta_texto', 'respuesta_opcion', 'respuesta_geografica'], 'string'],
            [['fecha_edicion'], 'safe'],
            [['fecha_edicion'], 'date', 'format' => 'yyyy-M-d H:m:s']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_proyecto' => 'Proyecto',
            'id_pregunta' => 'Pregunta',
            'respuesta_numerica' => 'Respuesta Númerica',
            'respuesta_texto' => 'Respuesta Texto',
            'respuesta_opcion' => 'Respuesta Opción',
            'respuesta_geografica' => 'Respuesta Geográfica',
            'ponderacion' => 'Ponderación',
            'fecha_edicion' => 'Fecha de Edición',
        ];
    }

    /**
     * Establece el campo fecha_edicion con la fecha actual
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->fecha_edicion = date('Y-m-d H:i:s');
            return true;
        } else {
            return false;
        }
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
    public function getProyecto()
    {
        return $this->hasOne(Proyecto::className(), ['id' => 'id_proyecto']);
    }

    /**
     *
     */
    public static function getByProyecto($id_proyecto)
    {
        return Respuesta::find()
            ->where(['id_proyecto' => $id_proyecto])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescripcionRespuesta()
    {
        $descripcion = null;
        $pregunta = $this->pregunta;

        switch ($pregunta->tipo_pregunta) {
            case 1: // Texto
            case 5: // Hipervínculo
                $descripcion[] = $this->respuesta_texto;
                break;

            case 2: // Numérica
                $descripcion[] = $this->respuesta_numerica;
                break;

            case 3: // Opción Múltiple
                $respuestas = json_decode($this->respuesta_opcion);

                $opciones = OpcionMultiple::find()
                    ->where('id IN ('.implode(',', $respuestas).')')
                    ->all();

                foreach ($opciones as $opcion) {
                    $descripcion[] = $opcion->descripcion;
                }

                break;

            case 4: // Opción Única
                $opcion = OpcionMultiple::findOne($this->respuesta_opcion);
                $descripcion[] = $opcion->descripcion;

                break;

            case 6: // Punto Radial Geográfico
            case 7: // Polígono Geográfico
            case 8: // Punto Geográfico
                $opcion = json_decode($this->respuesta_geografica);
                $descripcion[] = count($opcion) > 1 ? count($opcion).' puntos geográficos' : count($opcion).' punto geográfico';

                break;

            default:
                $descripcion[] = $this->respuesta_texto;

        }

        return $descripcion;
    }
    
    public static function checkSimMult($ar1, $ar2){
        $respuesta1 = $ar1[0];
        $respuesta2 = $ar2[0];
        $totalp = sizeof(OpcionMultiple::find()->where(['id_pregunta' => $respuesta1->id_pregunta])->all());
        $rarray1 = json_decode($respuesta1->respuesta_opcion);
        $rarray2 = json_decode($respuesta2->respuesta_opcion);
        $a = sizeof($rarray1);
        $b = sizeof($rarray2);
        //echo "total:".$totalp." ";
        
        $coincidencias = array_intersect($rarray1, $rarray2);
        $c= sizeof($coincidencias);
        
        //Elejir el mayor para dividirlo
        if($a > $b)
            $mayor = $a;
        else
            $mayor = $b;
        
        $porcentaje = $c*100/$mayor;
        return $porcentaje;
    }
    
    public static function checkSimOnly($ar1, $ar2){
        $respuesta1 = $ar1[0];
        $respuesta2 = $ar2[0];
        $totalp = sizeof(OpcionMultiple::find()->where(['id_pregunta' => $respuesta1->id_pregunta])->all());
        $r1 = $respuesta1->respuesta_opcion;
        $r2 = $respuesta2->respuesta_opcion;
        
        if($r1 == $r2)
            return 100;
        return 0;
    }
}
