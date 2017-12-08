<?php

namespace app\controllers;

use Yii;
use app\models\Usuario;
use app\models\Proyecto;
use app\models\Pregunta;
use app\models\Respuesta;
use app\models\OpcionMultiple;
use app\models\LoginForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * AdministradorController implements the CRUD actions for Administrador model.
 */
class AdministradorController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Administrador';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Administradores';
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'concursos', 'preguntasrubrica', 'filtrospreguntas', 'filtrosparticipantes', 'perfil', 'mailconcurso', 'checksimilar', 'getproyectoscompletados'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return Yii::$app->user->identity->isAdministrador();
                            }

                            return false;
                        },
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect([Usuario::$HOME[Usuario::$ADMINISTRADOR]]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionConcursos()
    {
        return $this->render('concursos', []);
    }

    /**
     * 
     * @return mixed
     */
    public function actionPreguntasrubrica($id)
    {
        return $this->render('preguntas_rubrica', [
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionFiltrospreguntas($id)
    {
        return $this->render('filtros_preguntas', [
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionFiltrosparticipantes($id)
    {
        return $this->render('filtros_participantes', [
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Usuario::$ADMINISTRADOR]]);
        }

        $model = new LoginForm();
        $model->role = Usuario::$ADMINISTRADOR;
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect([Usuario::$HOME[Usuario::$ADMINISTRADOR]]);
        }
        
        return $this->render('//site/login', [
            'model' => $model,
        ]);
    }
    
    public function actionPerfil(){
        
        $catalogos = Usuario::getCatalogos();
        
        return $this->render('//templates/perfil-usuario-tpl', ['catalogos' => $catalogos]);
    }
    
    public function actionMailconcurso(){
        $usuarios = Usuario::find()->where(['tipo' => 2])->all();
        foreach($usuarios as $usuario){
            echo $usuario->email;
            /*
            $e = Yii::$app->mailer->compose('notificacion_concurso')
                ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                ->setTo("gabrielrmnd@gmail.com")
                ->setSubject(Yii::$app->params['title'] . ': Aplica a un nuevo concurso')
                ->send();*/
        }
        
    }
    
    public function actionChecksimilar($id)
    {
        $proyectoA = Proyecto::findOne($id);
        
        if($proyectoA->isCompletado()){
            echo "Proyecto actual completado. <br>";
            $respuestasA = Respuesta::find()->where(['id_proyecto' => $id])->orderBy('id_pregunta')->all();
                    
            //Paso 1 Datos generales-Etiquetas
            //$proyectoA->etiquetas_array
            //echo var_dump($respuestasA);
            $proyectos1 = Proyecto::find()->where(['etiquetas_array' => $proyectoA->etiquetas_array])->all();
            
            $opciones = OpcionMultiple::find()->all();
            $suma = 0;
            $total_ideal = 0;
            foreach($proyectos1 as $proyecto){
                if($proyecto->isCompletado() && $proyecto->id != $id){
                    
                    //Paso 2 Problema-Describe las áreas en las que impactan estos problemas
                    //pregunda id = 2
                    $preguntas = [[2,3],[7,2],[9,2],[54,1],[55,2]]; //opciones multiples
                    foreach($preguntas as $pregunta){
                        $pregunta_modelo = Pregunta::findOne(['id' => $pregunta[0]]);
                        $tipo_pregunta = $pregunta_modelo->tipo_pregunta;
                        
                        $respuesta_p2 = Respuesta::find()->where(['id_proyecto' => $id, 'id_pregunta' => $pregunta[0]])->all();
                        $respuesta_comp = Respuesta::find()->where(['id_proyecto' => $proyecto->id, 'id_pregunta' => $pregunta[0]])->all();
                        
                        //echo $respuesta_p2[0]->respuesta_opcion." ";
                        //echo $respuesta_comp[0]->respuesta_opcion." ";
                        //echo $tipo_pregunta. "  ";
                        //echo $pregunta[1]." ";
                        switch($tipo_pregunta){
                            case 3: //multiple
                                $points = Respuesta::checkSimMult($respuesta_p2, $respuesta_comp);
                                break;
                            case 4: //unica
                                $points = Respuesta::checkSimOnly($respuesta_p2, $respuesta_comp);
                                break;
                        }
                        $suma += $points * $pregunta[1];
                        $total_ideal += 100 * $pregunta[1];
                        //echo $points;
                        //echo "<br>";
                    }
                    $preguntas = [];
                }
                
            }
            //echo "suma: ".$suma;
            //echo "total: ".$total_ideal;
            $similitud = $suma * 100 / $total_ideal;
            //echo "<br>";
            echo "similitud del ".$similitud."%";
        }
    }
    
    public function actionGetproyectoscompletados()
    {
        $preguntas = Pregunta::find()->all();
        $total_preguntas = sizeof($preguntas);
        echo $total_preguntas;
        
        $query = 'SELECT id_proyecto, count(id_pregunta) AS resp FROM `respuestas` WHERE 1 GROUP BY id_proyecto';
        $result = Yii::$app->db->createCommand($query)->queryAll();
        //echo var_dump($result);
        foreach($result as $respuesta){
            echo $respuesta['resp']."<br>";
        }
        
    }
    
    
}
