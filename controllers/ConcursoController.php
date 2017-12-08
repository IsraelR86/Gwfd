<?php

namespace app\controllers;

use Yii;
use app\models\Concurso;
use app\models\ConcursoAplicado;
use app\models\ConcursoSearch;
use app\models\ConcursantesSearch;
use app\models\GrupoEvaluadores;
use app\models\GruposEvXEvaluadores;
use app\models\GruposEvXProyectos;
use app\models\GrupoEvaluadoresSearch;
use app\models\Estado;
use app\models\Universidad;
use app\models\ProyectoSearch;
use app\models\Rubrica;
use app\models\Proyecto;
use app\models\RespuestaConcurso;
use app\models\Seccion;
use app\models\PreguntaXConcurso;
use app\models\Usuario;
use app\models\EtiquetasXConcurso;
use app\models\Evaluador;
use app\models\EvaluadorXInstitucion;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\helpers\Functions;

class ConcursoController extends \yii\web\Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Concurso';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Concursos';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['ganadores'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['view', 'downloadbases'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['validrules', 'getbyid', 'getpuntaje', 'getrubricas', 'getall'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['getbyemprendedor', 'aplicar', 'getallavailables', 'getaplicacion', 'getpreguntasconcurso', 'setpreguntas', 'abandonar'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return (
                                    Yii::$app->user->identity->isEmprendedor() ||
                                    Yii::$app->user->identity->isAdministrador() ||
                                    Yii::$app->user->identity->isEvaluador());
                            }

                            return false;
                        },
                    ],
                    [
                        'actions' => ['index', 'concursantes', 'gposevaluadores', 'procesarfiltros', 'filtradoproyectos', 'resultsecciones'],
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
                        'actions' => ['getbyinstitucion', 'setevaluadores', 'cancelar', 'publicar', 'getevaluacionesbyevaluador'],
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
                        'actions' => ['set', 'uploadbases', 'setpreguntasconcurso', 'getevaluadores', 'setrubricas', 'asignarevaluadores'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return Yii::$app->user->identity->isInstitucion() || Yii::$app->user->identity->isAdministrador();
                            }

                            return false;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Concurso models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ConcursoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Displays a single Concurso model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Finds the Concurso model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Concurso the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Concurso::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('La página solicitada no existe.');
        }
    }

    public function actionValidrules()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // Permite Cross Domain Requests
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');

        $concurso = Concurso::findOne(Yii::$app->request->post('concurso'));

        if ($concurso == null) {
            return ['concurso' => 'Concurso no disponible'];
        }

        return $concurso->checkRulesToApply(Yii::$app->user->identity->emprendedor);
    }

    public function actionConcursantes($id)
    {
        $concurso = $this->findModel($id);
        $searchConcursantes = new ConcursantesSearch();
        $searchConcursantes->setConcurso($id);
        $dpConcursantes = $searchConcursantes->search(Yii::$app->request->queryParams);
        $optionsDocumentos = [
            0 => 'Pendiente',
            1 => 'Recibido',
            2 => 'No recibido',
        ];
        $optionsAplicacion = [
            0 => 'Pendiente',
            1 => 'Aprobada',
            2 => 'Rechazada',
        ];
        $estados = ArrayHelper::map(
            Estado::find()
            ->select('id, descripcion')
            ->all(),
            'id', 'descripcion'
        );
        $universidades = ArrayHelper::map(
            Universidad::find()
            ->select('id, nombre')
            ->where('activo = 1')
            ->orderBy('nombre')
            ->all(),
            'id', 'nombre'
        );

        return $this->render('concursantes', [
            'concurso' => $concurso,
            'searchConcursantes' => $searchConcursantes,
            'dpConcursantes' => $dpConcursantes,
            'optionsDocumentos' => $optionsDocumentos,
            'optionsAplicacion' => $optionsAplicacion,
            'universidades' => $universidades,
            'estados' => $estados,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    public function actionGposevaluadores($id)
    {
        $model = $this->findModel($id);
        $gpoevaluadores = new GrupoEvaluadores();
        $searchModel = new GrupoEvaluadoresSearch();
        $dpGposevaluadores = $searchModel->search(['GrupoEvaluadoresSearch' => ['id_concurso' => $id]]);
        $gpoevaluadores->id_concurso = $id;
        $gpoevaluadores->fecha_alta = date('Y-m-d H:i:s');

        return $this->render('gposevaluadores', [
            'model' => $model,
            'dpGposevaluadores' => $dpGposevaluadores,
            'gpoevaluadores' => $gpoevaluadores,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    public function actionProcesarfiltros()
    {
        //$proyectosAprobados = [];
        //$proyectosNoAprobados = [];
        $filtros = [];

        if (Yii::$app->request->isPost) {
            $concurso = $this->findModel(Yii::$app->request->post('concurso'));

            // Primero se obtienen los proyectos que no alcanzarón el mínimo de puntuación
            // requerido por el concurso (concursos.calificacion_minima_proyectos)
            $proyectosNoAprobados = $concurso->getProyectosNoAprobadosFromCuestionario();

            foreach ($proyectosNoAprobados as $proyecto) {
                $concursoAplicado = $proyecto->getConcursoAplicado($concurso->id);
                $chkDocumentos = $proyecto->getChecklistDocumentos($concurso->id);

                $concursoAplicado->paso_filtros = 0;
                $concursoAplicado->calificacion = $proyecto->sumRespuestasPonderacion == null ? 0 : $proyecto->sumRespuestasPonderacion;
                $concursoAplicado->filtros_no_pasados = $this->crearFiltroNoPasado(0, "La puntuación total del Cuestionario NO alcanzó la calificación mínima requerida por el concurso"); //'{"id": 0, "descripcion": "La puntuación total del Cuestionario NO alcanzó la calificación mínima requerida por el concurso"}';

                $concursoAplicado->save();

                // También actualizamos la variable bandera para que NO pueda enviar su plan de negocios
                if ($chkDocumentos != null) {
                    $chkDocumentos->puede_enviar_plan = 0;
                    $chkDocumentos->save();
                }
            }

            // Se obtiene todos los proyectos rechazados
            $proyectosRechazados = $concurso->getProyectosRechazados();

            foreach ($proyectosRechazados as $proyecto) {
                $concursoAplicado = $proyecto->getConcursoAplicado($concurso->id);
                $chkDocumentos = $proyecto->getChecklistDocumentos($concurso->id);

                $concursoAplicado->paso_filtros = 0;
                $concursoAplicado->calificacion = $proyecto->sumRespuestasPonderacion == null ? 0 : $proyecto->sumRespuestasPonderacion;
                $concursoAplicado->filtros_no_pasados = $this->crearFiltroNoPasado(0, $chkDocumentos->aplicacion_aprobada == 0 ? "El proyecto no fue aprobado oficialmente por Administrador en pantalla de concursantes" : $chkDocumentos->motivo_rechazo_aplicacion ); //'{"id": 0, "descripcion": "'.$chkDocumentos->motivo_rechazo_aplicacion.'"}';

                $concursoAplicado->save();

                // También actualizamos la variable bandera para que NO pueda enviar su plan de negocios
                if ($chkDocumentos != null) {
                    $chkDocumentos->puede_enviar_plan = 0;
                    $chkDocumentos->save();
                }
            }

            // Reseteamos esta variable para poder guardar los proyectos que no pasen algún filtro automático
            $proyectosNoAprobados = [];
            // Obtenemos todos los proyectos que aprobaron el filtro de calificación mínima (concursos.calificacion_minima_proyectos)
            $proyectosAprobadosFromCuestionario = $concurso->getProyectosAprobadosFromCuestionario();
            // Obtnemos todos los filtros que aplican al concurso
            $filtros = $concurso->filtrosConcurso;

            // Recorrer todos los proyectos que aprobaron el filtro de calificación mínima (concursos.calificacion_minima_proyectos)
            foreach ($proyectosAprobadosFromCuestionario as $proyecto) {
                // Variable bandera para determinar si fueron aprobados todos los filtros
                $pasoFiltros = true;
                $concursoAplicado = $proyecto->getConcursoAplicado($concurso->id);
                $chkDocumentos = $proyecto->getChecklistDocumentos($concurso->id);

                // Para cada proyecto, evaluamos todos los filtros
                foreach ($filtros as $filtro) {
                    if ($filtro->evaluar($proyecto) == false) {
                        // En caso de NO aprobar un filtro, el proyecto es descartado
                        $pasoFiltros = false;

                        $concursoAplicado->paso_filtros = 0;
                        $concursoAplicado->calificacion = $proyecto->sumRespuestasPonderacion;
                        $concursoAplicado->filtros_no_pasados = $this->crearFiltroNoPasado($filtro->id, $filtro->comentarios); //'{"id": '.$filtro->id.', "descripcion": "'.$filtro->comentarios.'"}';

                        // También actualizamos la variable bandera para que NO pueda enviar su plan de negocios
                        if ($chkDocumentos != null) {
                            $chkDocumentos->puede_enviar_plan = 0;
                        }

                        // Solo basta con que NO pase un filtro para descartar al proyecto
                        break; // No es necesario continuar con los siguientes filtros
                    }
                }

                // En caso de haber aprobado todos los filtros
                if ($pasoFiltros) {
                    $concursoAplicado->paso_filtros = 1;
                    $concursoAplicado->calificacion = $proyecto->sumRespuestasPonderacion;
                    $concursoAplicado->filtros_no_pasados = '';

                    // También actualizamos la variable bandera para que pueda enviar su plan de negocios
                    if ($chkDocumentos != null) {
                        $chkDocumentos->puede_enviar_plan = 1;
                    }
                }

                $concursoAplicado->save();

                if ($chkDocumentos != null) {
                    $chkDocumentos->save();
                }
            }

            Yii::$app->session->setFlash('result', [
                'message' => 'Proceso ejecutado exitosamente',
            ]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Helper para actionProcesarFiltros
     */
    private function crearFiltroNoPasado($id, $descripcion){
        return json_encode( ["id"=> $id, "descripcion"=> $descripcion] , JSON_UNESCAPED_UNICODE);
    }

    public function actionFiltradoproyectos($id)
    {
        $model = $this->findModel($id);
        $searchProyectos = new ProyectoSearch();
        $searchProyectos->id_concurso = $id;
        $dpProyectos = $searchProyectos->search(Yii::$app->request->queryParams);
        $dpProyectos->pagination = false;

        return $this->render('filtradoproyectos', [
            'model' => $model,
            'searchProyectos' => $searchProyectos,
            'dpProyectos' => $dpProyectos,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    public function actionResultsecciones($id)
    {
        $model = $this->findModel($id);
        $secciones = Seccion::find()
            ->orderBy('id ASC')
            ->all();

        return $this->render('resultsecciones', [
            'model' => $model,
            'secciones' => $secciones,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }


    public function actionGetallavailables()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $concursos = Concurso::getAllAvailables(Yii::$app->request->get('page'));
        $arrConcursos = ArrayHelper::toArray($concursos, [
                                'app\models\Concurso' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteImagen',
                                    'fechaCierre',
                                    'etiquetas',
                                    'institucion',
                                ],
                                'app\models\Etiqueta' => [
                                    'descripcion',
                                ],
                                'app\models\Institucion' => [
                                    'nombre',
                                ]
                            ]);

        return [
            'total' => count($arrConcursos),
            'result' => $arrConcursos
        ];
    }

    public function actionGetbyinstitucion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $filtro = ['cancelado' => null];

        if (Yii::$app->request->get('finalizados')) {
            $filtro = ['<', 'fecha_cierre', date('Y-m-d')];
        }

        $concursos = Concurso::getAllByInstitucion(Yii::$app->user->identity->institucion->id, Yii::$app->request->get('page'), 5, $filtro);
        $arrConcursos = ArrayHelper::toArray($concursos, [
                                'app\models\Concurso' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteImagen',
                                    'fechaCierre',
                                    'etiquetas',
                                    'institucion',
                                    'cancelado',
                                    'status',
                                    'evaluadores_x_proyecto',
                                ],
                                'app\models\Etiqueta' => [
                                    'descripcion',
                                ],
                                'app\models\Institucion' => [
                                    'nombre',
                                ]
                            ]);

        return [
            'total' => count($arrConcursos),
            'result' => $arrConcursos
        ];
    }

    public function actionGetall()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $concursos = Concurso::getAll(Yii::$app->request->get('page'));
        $arrConcursos = ArrayHelper::toArray($concursos, [
                                'app\models\Concurso' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteImagen',
                                    'fechaCierre',
                                    'etiquetas',
                                    'institucion',
                                    'cancelado',
                                    'status',
                                ],
                                'app\models\Etiqueta' => [
                                    'descripcion',
                                ],
                                'app\models\Institucion' => [
                                    'nombre',
                                ]
                            ]);

        return [
            'total' => count($arrConcursos),
            'result' => $arrConcursos
        ];
    }


    /*public function actionGetallbyevaluador()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $concursos = Concurso::getAll(Yii::$app->request->get('page'));
        $arrConcursos = ArrayHelper::toArray($concursos, [
                                'app\models\Concurso' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteImagen',
                                    'fechaCierre',
                                    'etiquetas',
                                    'institucion',
                                    'cancelado'
                                ],
                                'app\models\Etiqueta' => [
                                    'descripcion',
                                ],
                                'app\models\Institucion' => [
                                    'nombre',
                                ]
                            ]);

        return [
            'total' => count($arrConcursos),
            'result' => $arrConcursos
        ];
    }*/
    public function actionGetbyid()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $concurso = null;
        $evaConcurso = null;

        if (Yii::$app->user->identity->tipo == Usuario::$INSTITUCION) {
            $concurso = Concurso::getById(Yii::$app->request->post('id'), Yii::$app->user->identity->institucion->id);
        } else {
            $concurso = Concurso::getById(Yii::$app->request->post('id'));
        }

        if ($concurso != null) {
            // Si el tipo de usuario es un evaluador
            // revisamos si este evaluador esta evaluando este concurso
            // es decir, si esta en la lista de evaluadores de la institución
            if (Yii::$app->user->identity->tipo == Usuario::$EVALUADOR) {
                $evaConcurso = GrupoEvaluadores::getByConcursoAndEvaluador(Yii::$app->request->post('id'), Yii::$app->user->identity->id);
            }

            $concurso = $concurso->toArray(
                    ['id', 'nombre', 'descripcion', 'bases', 'premios', 'cancelado', 'calificacion_minima_proyectos', 'fecha_resultados', 'evaluadores_x_proyecto', 'no_ganadores'], // Solo exportamos algunos atributos
                    ['etiquetas', 'institucion', 'byteImagen', 'fechaCierre', 'fechaArranque', 'status', 'noEvaluadores', 'noPlagios', 'proyectosRegistrados', 'proyectosCompletados', 'superanEvaluacionATM', 'posiblesPlagios', 'preguntas', 'countProyectosEvaluados', 'countProyectosAEvaluador'] // Se incluyen algunos campos extras en el arreglo
                );

            if (!$evaConcurso) {
                $concurso['evalua_concurso'] = false;
            } else {
                $concurso['evalua_concurso'] = true;
            }
        }

        return $concurso;
    }

    public function actionAplicar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Proyecto registrado exitosamente al concurso',
        ];
        $proyecto = null;

        // Validamos que el proyecto pertenesca al usuario logueado
        if (($proyecto = Proyecto::getByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->post('proyecto'))) == null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto no le pertenece';
        } else if (ConcursoAplicado::find()->where('id_concurso='.(int)Yii::$app->request->post('concurso').' AND id_proyecto='.(int)Yii::$app->request->post('proyecto'))->one() != null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto ya está inscrito al concurso seleccionado';
        } else {
            /*$aplicacion = new ConcursoAplicado();

            $aplicacion->id_concurso = Yii::$app->request->post('concurso');
            $aplicacion->id_proyecto = Yii::$app->request->post('proyecto');
            $aplicacion->fecha_alta = date('Y-m-d H:i:s');
            $aplicacion->createClave();

            if ($aplicacion->save()) {
                RespuestaConcurso::copyRespuestas(Yii::$app->request->post('concurso'), Yii::$app->request->post('proyecto'));
            */
                // En caso de ser exitoso el registro de aplicación devolvemos las preguntas del concurso
                $result = Concurso::getById(Yii::$app->request->post('concurso'))
                                ->toArray(
                                    ['id', 'nombre', 'descripcion'], // Solo exportamos algunos atributos
                                    ['etiquetas', 'byteImagen', 'fechaCierre', 'preguntas'] // Se incluyen algunos campos extras en el arreglo
                                );

                // Enviar correo electrónico
                /*Yii::$app->mailer->compose('nueva_aplicacion', [
                    'fecha' => date('d-m-Y H:i:s'),
                    'id_concurso' => $result['id'],
                    'concurso' => $result['nombre'],
                    'proyecto' => $proyecto->nombre,
                    'folio' => $aplicacion->clave,
                ])
                ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                ->setTo(Yii::$app->user->identity->email)
                ->setSubject(Yii::$app->params['title'] . ': Aplicación a concurso')
                ->send();*/

                $result['error'] = false;
                $result['proyecto'] = Yii::$app->request->post('proyecto');
            /*} else {
                $result['error'] = true;
                $result['message'] = Functions::errorsToString($aplicacion->errors);
            }*/
        }

        return $result;
    }

    /*public function actionConfirmaplicar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Proyecto registrado exitosamente al concurso',
        ];
        $proyecto = null;

        // Validamos que el proyecto pertenesca al usuario logueado
        if (($proyecto = Proyecto::getByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->post('proyecto'))) == null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto no le pertenece';
        } else if (ConcursoAplicado::find()->where('id_concurso='.(int)Yii::$app->request->post('concurso').' AND id_proyecto='.(int)Yii::$app->request->post('proyecto'))->one() != null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto ya está inscrito al concurso seleccionado';
        } else {
            $aplicacion = new ConcursoAplicado();

            $aplicacion->id_concurso = Yii::$app->request->post('concurso');
            $aplicacion->id_proyecto = Yii::$app->request->post('proyecto');
            $aplicacion->fecha_alta = date('Y-m-d H:i:s');
            $aplicacion->createClave();

            if ($aplicacion->save()) {
                RespuestaConcurso::copyRespuestas(Yii::$app->request->post('concurso'), Yii::$app->request->post('proyecto'));

                // En caso de ser exitoso el registro de aplicación devolvemos las preguntas del concurso
                $result = Concurso::getById(Yii::$app->request->post('concurso'))
                                ->toArray(
                                    ['id', 'nombre', 'descripcion'], // Solo exportamos algunos atributos
                                    ['etiquetas', 'byteImagen', 'fechaCierre', 'preguntas'] // Se incluyen algunos campos extras en el arreglo
                                );

                // Enviar correo electrónico
                Yii::$app->mailer->compose('nueva_aplicacion', [
                    'fecha' => date('d-m-Y H:i:s'),
                    'id_concurso' => $result['id'],
                    'concurso' => $result['nombre'],
                    'proyecto' => $proyecto->nombre,
                    'folio' => $aplicacion->clave,
                ])
                ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                ->setTo(Yii::$app->user->identity->email)
                ->setSubject(Yii::$app->params['title'] . ': Aplicación a concurso')
                ->send();

                $result['error'] = false;
                $result['proyecto'] = Yii::$app->request->post('proyecto');
            } else {
                $result['error'] = true;
                $result['message'] = Functions::errorsToString($aplicacion->errors);
            }
        }

        return $result;
    }*/

    public function actionGetbyemprendedor()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $concursos = ConcursoAplicado::getByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->get('page'));
        $arrConcursos = ArrayHelper::toArray($concursos, [
                                'app\models\ConcursoAplicado' => [
                                    'proyecto',
                                    'concurso',
                                ],
                                'app\models\Concurso' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteImagen',
                                    'fechaCierre',
                                ],
                                'app\models\Proyecto' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                ],
                            ]);

        return [
            'total' => count($arrConcursos),
            'result' => $arrConcursos
        ];
    }

    public function actionGetaplicacion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $aplicacion = ConcursoAplicado::getByAplicacion(
                Yii::$app->request->post('concurso'),
                Yii::$app->request->post('proyecto'),
                Yii::$app->user->identity->id);

        $arrayAplicacion = ArrayHelper::toArray($aplicacion, [
                                'app\models\ConcursoAplicado' => [
                                    'proyecto',
                                    'concurso',
                                ],
                                'app\models\Concurso' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteImagen',
                                    'fechaCierre',
                                    'premios',
                                    'status',
                                ],
                                'app\models\Proyecto' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                ],
                            ]);

        if (count($arrayAplicacion)) {
            return $arrayAplicacion[0];
        }

        return [];
    }

    public function actionGetaplicacionConcurso()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $aplicacion = ConcursoAplicado::getByAplicacion(
                Yii::$app->request->post('concurso'),
                Yii::$app->request->post('proyecto'),
                Yii::$app->user->identity->id);

        $arrayAplicacion = ArrayHelper::toArray($aplicacion, [
                                'app\models\ConcursoAplicado' => [
                                    'proyecto',
                                    'concurso',
                                ],
                                'app\models\Concurso' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteImagen',
                                    'fechaCierre',
                                    'premios',
                                    'status',
                                ],
                                'app\models\Proyecto' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                ],
                            ]);

        if (count($arrayAplicacion)) {
            return $arrayAplicacion[0];
        }

        return [];
    }

    public function actionDownloadbases()
    {
        $concurso = Concurso::findOne(Yii::$app->request->post('concurso'));
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $headers = Yii::$app->response->headers;

        if ($concurso == null) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = 'Concurso no encontrado';
            $headers->add('Content-Type', 'text/xml; charset=utf-8');
            return Yii::$app->response;
        }

        if ($concurso->downloadBases() == null) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->content = 'Archivo no encontrado';
            $headers->add('Content-Type', 'text/xml; charset=utf-8');
            return Yii::$app->response;
        }

        return Yii::$app->response->sendFile($concurso->downloadBases(), $concurso->nombreArchivoBases);
    }

    public function actionGetpreguntasconcurso()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $concurso = Concurso::getById(Yii::$app->request->post('id'))
                        ->toArray(
                            ['id', 'nombre', 'descripcion'], // Solo exportamos algunos atributos
                            ['etiquetas', 'byteImagen', 'fechaCierre', 'preguntas'] // Se incluyen algunos campos extras en el arreglo
                        );

        return $concurso;
    }

    public function actionSetpreguntas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Respuestas guardadas exitosamente',
        ];
        $proyecto = null;

        // Validamos que el proyecto pertenesca al usuario logueado
        if (($proyecto = Proyecto::getByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->post('proyecto'))) == null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto no le pertenece';
        }/* else if (ConcursoAplicado::find()->where('id_concurso='.(int)Yii::$app->request->post('concurso').' AND id_proyecto='.(int)Yii::$app->request->post('proyecto'))->one() == null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto no esta inscrito al concurso seleccionado';
        }*/ else {
            $aplicacion = new ConcursoAplicado();

            $aplicacion->id_concurso = Yii::$app->request->post('concurso');
            $aplicacion->id_proyecto = Yii::$app->request->post('proyecto');
            $aplicacion->fecha_alta = date('Y-m-d H:i:s');
            $aplicacion->createClave();

            if ($aplicacion->save()) {
                RespuestaConcurso::copyRespuestas(Yii::$app->request->post('concurso'), Yii::$app->request->post('proyecto'));

                // Enviar correo electrónico
                Yii::$app->mailer->compose('nueva_aplicacion', [
                    'fecha' => date('d-m-Y H:i:s'),
                    'id_concurso' => Yii::$app->request->post('concurso'),
                    'concurso' => $aplicacion->concurso->nombre,
                    'proyecto' => $proyecto->nombre,
                    'imagen_concurso' => $aplicacion->concurso->getByteimagen(),
                    'folio' => $aplicacion->clave,
                ])
                ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                ->setTo(Yii::$app->user->identity->email)
                ->setSubject(Yii::$app->params['title'] . ': Aplicación a concurso')
                ->send();
            } else {
                $result['error'] = true;
                $result['message'] = Functions::errorsToString($aplicacion->errors);
            }

            if (!$result['error']) {
                $list_respuestas = Yii::$app->request->post('list_respuestas');
                $id_proyecto = Yii::$app->request->post('proyecto');

                if (count($list_respuestas)) {
                    foreach($list_respuestas as $respuesta) {
                        $valRespuesta = $respuesta['respuesta'];

                        $pregunta = PreguntaXConcurso::findOne($respuesta['id_pregunta']);

                        if ($pregunta == null) {
                            $result['error'] = true;
                            $result['message'] = 'Pregunta no disponible';

                            return $result;
                        }

                        $respuesta = new RespuestaConcurso();
                        $respuesta->id_concurso = Yii::$app->request->post('concurso');
                        $respuesta->id_proyecto = $id_proyecto;
                        $respuesta->id_pregunta = $pregunta->id;
                        $respuesta->solo_concurso = 1;

                        $respuesta->respuesta_texto = $valRespuesta;
                        $respuesta->ponderacion = 1;

                        if (!$respuesta->save()) {
                            $result['error'] = true;
                            $result['message'] = 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($respuesta->errors).'</ul>';
                        }
                    }
                }
            }
        }


        return $result;
    }

    public function actionAbandonar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Abandonaste el concurso de forma exitosa.',
        ];

        // Validamos que el proyecto pertenesca al usuario logueado
        if (Proyecto::getByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->post('proyecto')) == null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto no le pertenece';
        } else {
            $aplicacion = ConcursoAplicado::find()->where('id_concurso='.(int)Yii::$app->request->post('concurso').' AND id_proyecto='.(int)Yii::$app->request->post('proyecto'))->one();

            if ($aplicacion == null) {
                $result['error'] = true;
                $result['message'] = 'No se encuentra disponible la aplicación';
            } else {
                $aplicacion->abandonar();
            }
        }
        return $result;
    }

    public function actionGetpuntaje()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => '',
        ];
        $evaluador = Yii::$app->user->identity->isEvaluador() ? Yii::$app->user->id : false;

        // Validamos que el proyecto pertenesca al usuario logueado
        /*if (Proyecto::getByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->post('proyecto')) == null) {
            $result['error'] = true;
            $result['message'] = 'El proyecto no le pertenece';
        } else {*/
            $concursoAplicado = ConcursoAplicado::find()->where('id_concurso='.(int)Yii::$app->request->post('concurso').' AND id_proyecto='.(int)Yii::$app->request->post('proyecto'))->one();

            if ($concursoAplicado == null) {
                $result['error'] = true;
                $result['message'] = 'El proyecto no esta inscrito al concurso seleccionado';
            } else {
                $result['id_proyecto'] = $concursoAplicado->id_proyecto;
                $result['nombre_proyecto'] = $concursoAplicado->proyecto->nombre;
                $result['puntaje'] = $concursoAplicado->getPuntajeByRubrica($evaluador);
                $result['error'] = false;
            }
        //}

        return $result;
    }

    /**
     *
     * @return mixed
     */
    public function actionGanadores($c)
    {
        $concurso = Concurso::findOne($c);

        return $this->render('ganadores', [
            'concurso' => $concurso,
        ]);
    }

    public function actionSet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();
        $result = ['error' => false, 'message' => 'Datos guardados exitosamente', 'id' => null, 'imagen' => null];

        try {
            $concurso = Concurso::findOne(Yii::$app->request->post('concurso'));

            if ($concurso == null) {
                $concurso = new Concurso();
            }

            $concurso->load(['Concurso' => Yii::$app->request->post()]);
            $concurso->fecha_arranque = Functions::transformDate(Yii::$app->request->post('fecha_arranque'), 'Y-m-d');
            $concurso->fecha_cierre = Functions::transformDate(Yii::$app->request->post('fecha_cierre'), 'Y-m-d');
            $concurso->id_institucion = Yii::$app->user->identity->institucion->id;

            if (empty($concurso->evaluadores_x_proyecto)) {
                $concurso->evaluadores_x_proyecto = 1;
            }

            $concurso->save();

            if ($concurso->errors) {
                $result['error'] = true;
                $result['message'] = json_encode($concurso->errors);
                return $result;
            }

            $result['id'] = $concurso->id;

            EtiquetasXConcurso::deleteAll(['id_concurso' => $concurso->id]);

            if (Yii::$app->request->post('etiquetas')) {
                foreach (Yii::$app->request->post('etiquetas') as $id_etiqueta) {
                    $etiqueta = EtiquetasXConcurso::find()
                        ->where(['id_concurso' => $concurso->id])
                        ->andWhere(['id_etiqueta' => $id_etiqueta])
                        ->one();

                    if ($etiqueta == null) {
                        $etiqueta = new EtiquetasXConcurso();
                        $etiqueta->id_concurso = $concurso->id;
                        $etiqueta->id_etiqueta = $id_etiqueta;
                        $etiqueta->save();
                    }
                }
            }

            $pathImagen = 'concurso'.DIRECTORY_SEPARATOR.$concurso->id.'.jpg';

            if (Functions::uploadFile('imagen', $pathImagen)) {
                $type = pathinfo($concurso->pathImagen, PATHINFO_EXTENSION);
                $imageByte = file_get_contents($concurso->pathImagen);
                $base64Foto = 'data:image/' . $type . ';base64,' . base64_encode($imageByte);
                $result['byteImagen'] = $base64Foto;
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $result['error'] = true;
            $result['message'] = json_encode(['DatosGenerales' => $e->getMessage()]);
        }

        return $result;
    }

    public function actionUploadbases()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $result = ['error' => false, 'message' => 'datos guardados exitosamente'];
        $concurso = null;

        if (Yii::$app->user->identity->tipo == Usuario::$INSTITUCION) {
            $concurso = Concurso::getById(Yii::$app->request->post('id'), Yii::$app->user->identity->institucion->id);
        } else {
            $concurso = Concurso::getById(Yii::$app->request->post('id'));
        }

        if (empty($concurso)) {
            $result['error'] = true;
            $result['message'] = 'El concurso no le pernetece';

            return $result;
        }

        try {
            $concurso->bases = $_FILES['bases']['name'];
            $concurso->save();

            Functions::uploadFile('bases', 'concurso/_'.Yii::$app->request->post('id').'_bases.pdf');
        } catch(\Exception $e) {
            $result['error'] = true;
            $result['message'] = json_encode(['logo' => $e->getMessage()]);
        }

        return $result;
    }

    public function actionSetpreguntasconcurso()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Concurso registrado exitosamente',
        ];

        // Validamos que el concurso pertenesca al usuario logueado
        if (Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id) == null) {
            $result['error'] = true;
            $result['message'] = 'El concurso no le pertenece';
        } else {
            $list_preguntas = Yii::$app->request->post('list_preguntas');
            $id_concurso = Yii::$app->request->post('id_concurso');

            if (count($list_preguntas)) {
                foreach($list_preguntas as $pregunta) {
                    $preguntaConcurso = PreguntaXConcurso::findOne($pregunta['id']);

                    if ($preguntaConcurso == null) {
                        $preguntaConcurso = new PreguntaXConcurso();
                    }

                    $preguntaConcurso->id_concurso = $id_concurso;
                    $preguntaConcurso->descripcion = $pregunta['descripcion'];
                    $preguntaConcurso->ayuda = isset($pregunta['ayuda']) ? $pregunta['ayuda'] : '';
                    $preguntaConcurso->id_tipo_pregunta_concurso = $pregunta['id_tipo_pregunta_concurso'];

                    if (!$preguntaConcurso->save()) {
                        $result['error'] = true;
                        $result['message'] = 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($preguntaConcurso->errors).'</ul>';

                        break;
                    }
                }
            }
        }

        return $result;
    }

    public function actionSetevaluadores()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Concurso registrado exitosamente',
        ];

        // Validamos que el concurso pertenesca al usuario logueado
        if (Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id) == null) {
            $result['error'] = true;
            $result['message'] = 'El concurso no le pertenece';
        } else {
            $list_evaluadores = Yii::$app->request->post('list_evaluadores');
            $id_concurso = Yii::$app->request->post('id_concurso');

            if (count($list_evaluadores)) {
                foreach($list_evaluadores as $evaluador) {
                    $gpoEvaluadorEvaluador = GrupoEvaluadores::getByConcursoAndEvaluador($id_concurso, $evaluador);

                    // Solo registramos aquellos evaluadores que no esten registrados
                    if ($gpoEvaluadorEvaluador == null) {
                        $usuario = Usuario::findOne($evaluador);
                        $grupoEvaluador = new GrupoEvaluadores();

                        $grupoEvaluador->id_concurso = $id_concurso;
                        $grupoEvaluador->nombre = substr($usuario->nombre_completo, 0, 44);
                        $grupoEvaluador->fecha_alta = date('Y-m-d H:i:s');

                        if (!$grupoEvaluador->save()) {
                            $result['error'] = true;
                            $result['message'] = 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($grupoEvaluador->errors).'</ul>';

                            break;
                        }

                        $gpoEvaluadoresXEvaluador = new GruposEvXEvaluadores();
                        $gpoEvaluadoresXEvaluador->id_grupo_evaluadores = $grupoEvaluador->id;
                        $gpoEvaluadoresXEvaluador->id_evaluador = $evaluador;
                        $gpoEvaluadoresXEvaluador->fecha_alta = date('Y-m-d H:i:s');

                        if (!$gpoEvaluadoresXEvaluador->save()) {
                            $result['error'] = true;
                            $result['message'] = 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($gpoEvaluadoresXEvaluador->errors).'</ul>';

                            break;
                        }
                    }
                }

                $gruposEliminar = Yii::$app->db->createCommand('SELECT id FROM grupos_evaluadores WHERE id_concurso = '.$id_concurso.' AND id NOT IN
                                (SELECT id_grupo_evaluadores FROM grupos_ev_x_evaluadores WHERE id_evaluador IN ('.implode($list_evaluadores, ',').'))')->queryAll();

                if (count($gruposEliminar)) {
                    $gruposEliminar = ArrayHelper::getColumn($gruposEliminar, 'id');

                    GruposEvXEvaluadores::deleteAll('id_grupo_evaluadores IN ('.implode($gruposEliminar, ',').')');
                    GrupoEvaluadores::deleteAll('id IN ('.implode($gruposEliminar, ',').')');
                }
            }
        }

        return $result;
    }

    public function actionCancelar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Concurso cancelado exitosamente',
        ];

        $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id);

        // Validamos que el concurso pertenesca al usuario logueado
        if ($concurso == null) {
            $result['error'] = true;
            $result['message'] = 'El concurso no le pertenece';
        } else {
            $concurso->cancelado = 1;
            $concurso->save();

            if ($concurso->errors) {
                $result['error'] = true;
                $result['message'] = 'ERROR: '.Functions::errorsToString($concurso->errors);
            }
        }

        return $result;
    }

    public function actionGetevaluadores()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $concurso = null;
        $evaluadores = null;

        if (Yii::$app->user->identity->tipo == Usuario::$INSTITUCION) {
            $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id);
        } else {
            $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'));
        }

        if ($concurso != null) {
            $evaluadores = $concurso->getEvaluadores();

            if (count($evaluadores)) {
                $evaluadores = ArrayHelper::toArray($evaluadores, [
                                'app\models\Usuario' => [
                                    'id',
                                    'nombre_completo',
                                    'byteimagen',
                                    'etiquetas'
                                ],
                            ]);
            }
        } else {
             $result['error'] = true;
            $result['message'] = 'Concurso no disponible';
        }

        return ['evaluadores' => $evaluadores];
    }

    public function actionGetrubricas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $concurso = null;
        $rubricas = null;
        $result = [];

        if (Yii::$app->user->identity->tipo == Usuario::$INSTITUCION) {
            $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id);
        } else {
            $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'));
        }

        if ($concurso != null) {
            $rubricas = $concurso->rubricas;
        } else {
             $result['error'] = true;
            $result['message'] = 'Concurso no disponible';
        }

        $result['rubricas'] = $rubricas;

        if (Yii::$app->request->post('extras')) {
            $result['concurso'] = [
                'id' => $concurso->id,
                'nombre' => $concurso->nombre,
                'byteImagen' => $concurso->byteImagen,
                'etiquetas' => $concurso->etiquetas,
            ];
        }

        return $result;
    }

    public function actionSetrubricas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Rúbricas registradas exitosamente',
        ];

        // Validamos que el concurso pertenesca al usuario logueado
        if (Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id) == null) {
            $result['error'] = true;
            $result['message'] = 'El concurso no le pertenece';
        } else {
            $list_rubricas = Yii::$app->request->post('list_rubricas');
            $rubricas_actuales = null;
            $id_concurso = Yii::$app->request->post('id_concurso');

            if (count($list_rubricas)) {
                foreach($list_rubricas as $rubrica) {
                    // Si la rubrica no tiene ID significa que todavia no esta insertada en la BDatos
                    if (empty($rubrica['id'])) {
                        $objRubrica = new Rubrica();

                        $objRubrica->load(['Rubrica' => $rubrica]);
                        $objRubrica->tipo = 1;
                        $objRubrica->id_concurso = $id_concurso;

                        if (!$objRubrica->save()) {
                            $result['error'] = true;
                            $result['message'] = 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($objRubrica->errors).'</ul>';

                            return $result;
                        }

                        $rubricas_actuales[] = $objRubrica->id;
                    } else {
                        $rubricas_actuales[] = $rubrica['id'];
                    }
                }

                // Eliminamos las rubricas que el usuario eliminó de la lista
                Rubrica::deleteAll('id NOT IN ('.implode($rubricas_actuales, ',').') AND id_concurso = '.$id_concurso);
            }
        }

        return $result;
    }

    public function actionPublicar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Los resultados del concurso se publicaron exitosamente',
        ];

        $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id);

        // Validamos que el concurso pertenesca al usuario logueado
        if ($concurso == null) {
            $result['error'] = true;
            $result['message'] = 'El concurso no le pertenece';
        } else {
            $concurso->fecha_resultados = date('Y-m-d');
            $concurso->save();

            if ($concurso->errors) {
                $result['error'] = true;
                $result['message'] = 'ERROR: '.Functions::errorsToString($concurso->errors);
            }
        }

        return $result;
    }

    public function actionGetevaluacionesbyevaluador()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $concurso = null;
        $evaluadores = null;

        if (Yii::$app->user->identity->tipo == Usuario::$INSTITUCION) {
            $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'), Yii::$app->user->identity->institucion->id);
        } else {
            $concurso = Concurso::getById(Yii::$app->request->post('id_concurso'));
        }

        if ($concurso != null) {
            $evaluadores = $concurso->getEvaluadores();

            if (count($evaluadores)) {
                $evaluadores = ArrayHelper::toArray($evaluadores, [
                                'app\models\Usuario' => [
                                    'id',
                                    'nombre_completo',
                                    'etiquetas'
                                ],
                            ]);

                foreach ($evaluadores as $index => $evaluador) {
                    $evaluaciones = Evaluador::getEvaluacionesByProyectos($concurso->id, $evaluador['id']);

                    $evaluadores[$index]['evaluaciones'] = $evaluaciones;
                    $sumPuntajeProyectos = 0;
                    $sumPuntajeTotal = 0;

                    if (count($evaluaciones)) {
                        foreach ($evaluaciones as $evaluacion) {
                            $sumPuntajeProyectos += $evaluacion['puntaje'];
                            $sumPuntajeTotal += $evaluacion['calificacion_maxima'];
                        }

                        $evaluadores[$index]['promedioPuntajeProyectos'] = round($sumPuntajeProyectos / count($evaluaciones));
                        $evaluadores[$index]['promedioPuntajeTotal'] = round($sumPuntajeTotal / count($evaluaciones));
                    }

                    $evaluadores[$index]['no_evaluados'] = count($evaluaciones);
                    $evaluadores[$index]['total_evaluar'] = Evaluador::getCountProyectosAEvaluar($concurso->id, $evaluador['id']);
                }
            }
        } else {
             $result['error'] = true;
            $result['message'] = 'Concurso no disponible';
        }

        return ['evaluadores' => $evaluadores];
    }

    public function actionAsignarevaluadores()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $proyectosAprobados = [];
        $result = [
            'error' => false,
            'message' => 'Proceso ejecutado exitosamente',
        ];

        try {
            if (Yii::$app->request->isPost) {
                $concurso = $this->findModel(Yii::$app->request->post('id_concurso'));

                //$concurso->evaluacionAutomatica();
                $concurso->asignarEvaluadores();
            }
        } catch(\Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

}
