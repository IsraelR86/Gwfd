<?php

namespace app\controllers;

use Yii;
use app\models\Usuario;
use app\models\Evaluador;
use app\models\GruposEvXEvaluadores;
use app\models\GrupoEvaluadores;
use app\models\GruposEvXProyectos;
use app\models\Proyecto;
use app\models\Evaluaciones;
use app\models\LoginForm;
use app\models\SingupForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use app\models\Concurso;
use app\models\Rubrica;
use app\models\Pregunta;
use app\models\Seccion;
use app\models\PreguntaXRubrica;
use app\helpers\Security;
use app\helpers\Functions;

/**
 * EvaluadorController implements the CRUD actions for Evaluador model.
 */
class EvaluadorController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Evaluador';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Evaluadores';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'singup'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['concursos', 'perfil', 'ver','misevaluaciones','evaluacionproyecto', 'index', 'aplicaconcurso'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return Yii::$app->user->identity->isAdministrador() || Yii::$app->user->identity->isEvaluador();
                            }

                            return false;
                        },
                    ],
                    [
                        'actions' => ['get'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // Redirecciona a Concursos
        return $this->redirect([Usuario::$HOME[Usuario::$EVALUADOR]]);
    }

    /**
     *
     * @return mixed
     */
    public function actionConcursos()
    {
        return $this->render('index');
    }

    /**
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Usuario::$EVALUADOR]]);
        }

        $model = new LoginForm();
        $model->role = Usuario::$EVALUADOR;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect([Usuario::$HOME[Usuario::$EVALUADOR]]);
        }

        return $this->render('//site/login', [
            'model' => $model,
        ]);
    }

    public function actionPerfil()
    {
        $catalogos = Usuario::getCatalogos();

        return $this->render('//templates/perfil-usuario-tpl', ['catalogos' => $catalogos]);
    }

    public function actionGet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $usuario = Usuario::find()->where(['id' => Yii::$app->request->post('id')])->all();
        $arrUsuario = [];

        if (count($usuario)) {
            $arrUsuario = ArrayHelper::toArray($usuario, [
                            'app\models\Usuario' => [
                                'id',
                                'nombre_completo',
                                'byteimagen',
                                'etiquetas',
                                'evaluador'
                            ],
                            'app\models\Evaluador' => [
                                'semblanza',
                            ],
                        ]);

            if (count($arrUsuario)) {
                $arrUsuario = $arrUsuario[0];

                $estadisticas = Yii::$app->user->identity->institucion->estadisticasEvaluador($arrUsuario['id']);

                $arrUsuario['proyectos_calificados'] = (int)$estadisticas['proyectos_calificados'];
                $arrUsuario['promedio_calificaciones'] = (int)$estadisticas['promedio_calificaciones'];
                $arrUsuario['concursos_activos'] = $estadisticas['concursos_activos'];
                $arrUsuario['concursos_pasados'] = $estadisticas['concursos_pasados'];
            }
        }

        return $arrUsuario;

    }

    public function actionVer()
    {
        $id = Yii::$app->user->id;
        $evaluador = Evaluador::find()
        ->with('usuario')
        ->with('universidad')
        ->where('id_usuario = '.$id)
        ->one();

        return $this->render('ver', [
            'evaluador' => $evaluador
        ]);
    }
    public function actionMisevaluaciones()
    {
        $evaluador = Yii::$app->user->id;

        return $this->render('evaluaciones', [
        ]);

    }

    public function actionEvaluacionproyecto()
    {
        $proyecto = Proyecto::findOne(['id' => Yii::$app->request->get('p')]);

        if (!$proyecto->validEvaluador(Yii::$app->user->id)) {
            throw new \yii\web\UnauthorizedHttpException('No tiene permitido evaluar este proyecto.');
        }

        return $this->render('evaluacionproyecto', [
            'proyecto' => $proyecto,
        ]);
    }

    public function actionAplicaconcurso()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'mensaje' => 'Aplicación exitosa',
        ];

        $aplica = Yii::$app->user->identity->evaluador->aplicaConcurso( Yii::$app->request->post('id_concurso') );

        if ($aplica !== true) {
            $result['error'] = true;
            $result['mensaje'] = $aplica;
        }

        return $result;
    }

    public function actionSingup()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
        }

        $model = new SingupForm();
        $model->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        $result = [
            'error' => false,
            'mensaje' => 'Registro exitoso',
        ];

        if (Yii::$app->request->isPost) {
            $usuario = new Usuario();
            $usuario->tipo = 3;
            $usuario->auth_token = Security::createToken();
            // Se establece el escenario para aplicar las reglas de validación correspondientes al registro de usuario
            $usuario->setScenario('signup');
            $usuario->load(['Usuario' => Yii::$app->request->post('SingupForm')]);
            $usuario->password = Security::encode($usuario->password);

            if ($usuario->save()) {
                $evaluador = new Evaluador();
                //$evaluador->setScenario('signup');
                $evaluador->id_usuario = $usuario->id;
                $evaluador->id_estado = 7;

                $evaluador->load(['Evaluador' => Yii::$app->request->post('SingupForm')]);

                if (!$evaluador->save()) {
                    $result['error'] = true;
                    $result['mensaje'] = Functions::errorsToString($evaluador->errors);
                    $transaction->rollBack();
                } else {
                    // Enviar correo electrónico
                  /*  Yii::$app->mailer->compose('nuevo_cliente', [])
                    ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                    ->setTo($usuario->email)
                    ->setSubject(Yii::$app->params['title'] . ': Bienvenido')
                    ->send();*/

                    $transaction->commit();
                }
            } else {
                $result['error'] = true;
                $result['mensaje'] = Functions::errorsToString($usuario->errors);
                $transaction->rollBack();
            }
        }

        return $this->render('singup', [
            'model' => $model,
            'result' => $result,
        ]);
    }

}
