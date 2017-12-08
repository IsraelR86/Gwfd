<?php

namespace app\controllers;

use Yii;
use app\models\Concurso;
use app\models\Usuario;
use app\models\LoginForm;
use app\models\Institucion;
use app\models\EvaluadorXInstitucion;
use app\models\PreguntaXConcurso;
use app\models\SingupForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\helpers\Security;
use app\helpers\Functions;

/**
 * InstitucionController implements the CRUD actions for Institucion model.
 */
class InstitucionController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Institucion';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Instituciones';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'concursos', 'findevaluador', 'evaluaciones', 'evaluadores', 'getevaluadores', 'delevaluador', 'perfil', 'ver', 'emprendedores'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return Yii::$app->user->identity->isInstitucion();
                            }

                            return false;
                        },
                    ],
                    [
                        'actions' => ['login', 'singup'],
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
        return $this->redirect([Usuario::$HOME[Usuario::$INSTITUCION]]);
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
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Usuario::$INSTITUCION]]);
        }

        $model = new LoginForm();
        $model->role = Usuario::$INSTITUCION;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect([Usuario::$HOME[Usuario::$INSTITUCION]]);
        }

        return $this->render('//site/login', [
            'model' => $model,
        ]);
    }

    public function actionFindevaluador()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $evaluadores = [];

        if (Yii::$app->request->get('term')) {
            $evaluadores = ArrayHelper::toArray(Yii::$app->user->identity->institucion->findEvaluadorByName(Yii::$app->request->get('term')), [
                                'app\models\Usuario' => [
                                    'id',
                                    'nombre_completo',
                                    'byteimagen',
                                    'etiquetas'
                                ],
                            ]);
        }

        return $evaluadores;
    }

    /**
     *
     * @return mixed
     */
    public function actionEvaluaciones()
    {
        return $this->render('evaluaciones', []);
    }

    /**
     *
     * @return mixed
     */
    public function actionEvaluadores()
    {
        return $this->render('evaluadores', []);
    }

    public function actionGetevaluadores()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $arrEvaluadores = [];

        $evaluadores = Yii::$app->user->identity->institucion->findEvaluadores(Yii::$app->request->get('page'));

        if ($evaluadores) {
            $arrEvaluadores = ArrayHelper::toArray($evaluadores, [
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
        }

        return [
            'total' => count($arrEvaluadores),
            'result' => $arrEvaluadores
        ];
    }

    public function actionDelevaluador()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Evaluador eliminado exitosamente',
        ];

        $evaluador = EvaluadorXInstitucion::find()
            ->where(['id_institucion' => Yii::$app->user->identity->institucion->id])
            ->andWhere(['id_evaluador' => Yii::$app->request->post('evaluador')])
            ->one();

        if ($evaluador) {
            $evaluador->delete();
        } else {
            $result = [
                'error' => true,
                'message' => 'Evaluador no disponible',
            ];
        }

        return $result;
    }

    public function actionPerfil(){

        $catalogos = Usuario::getCatalogos();

        return $this->render('perfil', ['catalogos' => $catalogos]);
    }

    public function actionVer($id)
    {
        $institucion = Institucion::find()
        ->with('concursos')
        ->where('id = '.$id)
        ->one();

        return $this->render('ver', [
            'institucion' => $institucion
        ]);
    }

    public function actionEmprendedores($id)
    {
        $concurso = Concurso::findOne($id);

        if (empty($concurso) || $concurso->id_institucion != Yii::$app->user->identity->institucion->id) {
            throw new NotFoundHttpException('Concurso no disponible.');
        }

        // Ejecuta la evaluación automatica
        $concurso->evaluacionAutomatica();

        $proyectos = $concurso->getProyectosAprobadosFromCuestionarioConcurso();
        $proyectosNoAprobados = $concurso->getProyectosNoAprobadosFromCuestionarioConcurso();
        $preguntas = PreguntaXConcurso::find()->where(['id_concurso' => $id])->all();

        return $this->render('emprendedores', [
            'concurso' => $concurso,
            'proyectos' => $proyectos,
            'proyectosNoAprobados' => $proyectosNoAprobados,
            'preguntas' => $preguntas,
        ]);
    }

    public function actionSingup()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
        }

        $model = new SingupForm(['scenario' => 'institucion']);
        $model->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        $result = [
            'error' => false,
            'mensaje' => 'Registro exitoso',
        ];

        if (Yii::$app->request->isPost) {
            $usuario = new Usuario();
            $usuario->tipo = 4;
            $usuario->auth_token = Security::createToken();
            // Se establece el escenario para aplicar las reglas de validación correspondientes al registro de usuario
            $usuario->setScenario('signup');
            $usuario->load(['Usuario' => Yii::$app->request->post('SingupForm')]);
            $usuario->password = Security::encode($usuario->password);

            if ($usuario->save()) {
                $institucion = new Institucion();
                //$institucion->setScenario('signup');
                $institucion->id_usuario = $usuario->id;
                $institucion->id_estado = 7;

                $institucion->load(['Institucion' => Yii::$app->request->post('SingupForm')]);
                $institucion->nombre = Yii::$app->request->post('SingupForm')['nombre_institucion'];

                if (!$institucion->save()) {
                    $result['error'] = true;
                    $result['mensaje'] = Functions::errorsToString($institucion->errors);
                    $transaction->rollBack();
                } else {
                    // Enviar correo electrónico
                    Yii::$app->mailer->compose('nuevo_cliente', [])
                    ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                    ->setTo($usuario->email)
                    ->setSubject(Yii::$app->params['title'] . ': Bienvenido')
                    ->send();

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
