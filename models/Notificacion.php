<?php

namespace app\models;

use Yii;

/**
 *
 */
class Notificacion extends \yii\db\ActiveRecord
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getFechasCalendario()
    {
        /*$query = 'SELECT
                id,
                nombre AS titulo,
                CASE
                    WHEN fecha_arranque >= CURDATE() THEN "Arranque"
                    WHEN fecha_cierre >= CURDATE()   THEN "Cierre"
                    WHEN fecha_resultados >= CURDATE()   THEN "Resultado"
                END AS subtitulo,
                CASE
                    WHEN fecha_arranque >= CURDATE() THEN fecha_arranque
                    WHEN fecha_cierre >= CURDATE()   THEN fecha_cierre
                    WHEN fecha_resultados >= CURDATE()   THEN fecha_resultados
                END AS fecha,
                CASE
                    WHEN LENGTH(descripcion) < 150  THEN descripcion
                    WHEN LENGTH(descripcion) >= 150 THEN CONCAT(SUBSTRING(descripcion, 1, 150), "...")
                END AS contenido

            FROM
                concursos
            WHERE (
                    fecha_arranque >= CURDATE()
                    OR fecha_cierre >= CURDATE()
                    OR fecha_resultados >= CURDATE()
                )
            ORDER BY fecha_arranque ASC,
                fecha_cierre ASC,
                fecha_resultados ASC
            LIMIT 50';*/
          $query = 'select
          	  id,
              group_concat(titulo separator ", ") as titulo,
              subtitulo,
              fecha,
              group_concat(concat("<strong>", titulo,"</strong>: ",contenido) separator "<br>") as contenido from (
          	SELECT
                id,
                nombre AS titulo,
                CASE
                    WHEN fecha_arranque >= CURDATE() THEN "Arranque"
                    WHEN fecha_cierre >= CURDATE()   THEN "Cierre"
                    WHEN fecha_resultados >= CURDATE()   THEN "Resultado"
                END AS subtitulo,
                CASE
                    WHEN fecha_arranque >= CURDATE() THEN fecha_arranque
                    WHEN fecha_cierre >= CURDATE()   THEN fecha_cierre
                    WHEN fecha_resultados >= CURDATE()   THEN fecha_resultados
                END AS fecha,
                CASE
                    WHEN LENGTH(descripcion) < 150  THEN descripcion
                    WHEN LENGTH(descripcion) >= 150 THEN CONCAT(SUBSTRING(descripcion, 1, 150), "...")
                END AS contenido

            FROM
                concursos
            WHERE (
                    fecha_arranque >= CURDATE()
                    OR fecha_cierre >= CURDATE()
                    OR fecha_resultados >= CURDATE()
                )
            ORDER BY fecha_arranque ASC,
                fecha_cierre ASC,
                fecha_resultados ASC
            LIMIT 50) AS tabla
            group by subtitulo, fecha';

        return Yii::$app->db->createCommand($query)->queryAll();
    }

    public static function getNotificacionesEvaluador($id_emprendedor)
    {
        $query = 'SELECT
                id,
                nombre AS titulo,
                CASE
                    WHEN fecha_arranque >= CURDATE()
                    THEN "Arranque"
                    WHEN fecha_cierre >= CURDATE()
                    THEN "Cierre"
                    WHEN fecha_resultados >= CURDATE()
                    THEN "Resultado"
                END AS subtitulo,
                CASE
                    WHEN fecha_arranque >= CURDATE()
                    THEN fecha_arranque
                    WHEN fecha_cierre >= CURDATE()
                    THEN fecha_cierre
                    WHEN fecha_resultados >= CURDATE()
                    THEN fecha_resultados
                END AS fecha,
                CASE
                    WHEN LENGTH(descripcion) < 150
                    THEN descripcion
                    WHEN LENGTH(descripcion) >= 150
                    THEN CONCAT(
                        SUBSTRING(descripcion, 1, 150),
                        "..."
                    )
                END AS contenido
            FROM
                concursos
            WHERE (
                    fecha_arranque >= CURDATE()
                    OR fecha_cierre >= CURDATE()
                    OR fecha_resultados >= CURDATE()
                )
                AND id IN
                (SELECT
                    ca.id_concurso
                FROM
                    concursos_aplicados ca,
                    proyectos p
                WHERE p.id = ca.id_proyecto
                    AND p.id_emprendedor_creador = '.$id_emprendedor.')
            ORDER BY fecha_arranque ASC,
                fecha_cierre ASC,
                fecha_resultados ASC
            LIMIT 50 ';

        return Yii::$app->db->createCommand($query)->queryAll();
    }

    public static function getConcursosActivos()
    {
        $query = 'SELECT
                id,
                nombre AS titulo,
                CASE
                    WHEN LENGTH(descripcion) < 150
                    THEN descripcion
                    WHEN LENGTH(descripcion) >= 150
                    THEN CONCAT(
                        SUBSTRING(descripcion, 1, 150),
                        "..."
                    )
                END AS contenido
            FROM
                concursos
            WHERE (
                    fecha_cierre >= CURDATE()
                    AND fecha_arranque <= CURDATE()
                    OR fecha_resultados >= CURDATE()
                )
            ORDER BY fecha_arranque ASC,
                fecha_cierre ASC
            LIMIT 50';

        return Yii::$app->db->createCommand($query)->queryAll();
    }
}
