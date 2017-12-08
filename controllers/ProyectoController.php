<?php

namespace app\controllers;

use Yii;
use app\models\Emprendedor;
use app\models\Usuario;
use app\models\Concurso;
use app\models\ConcursoAplicado;
use app\models\EmprendedorXProyecto;
use app\models\Proyecto;
use app\models\Pregunta;
use app\models\Respuesta;
use app\models\EtiquetasXProyecto;
use app\models\ChecklistDocumentos;
use app\models\Seccion;
use app\models\Ganadores;
use app\models\PreguntaXConcurso;
use app\models\RespuestaConcurso;
use app\models\Badge;
use app\models\BadgeXUsuario;
use app\helpers\Security;
use app\helpers\Functions;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * ProyectoController implements the CRUD actions for Emprendedor model.
 */
class ProyectoController extends Controller
{
    //public $layout = 'aceadmin';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['micrositio'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['addintegrante', 'updateintegrante', 'getintegrante', 'uploadplannegocios', 'downloadplannegocios', 'downloadrespuestaarchivo', 'setstatusaplicacion'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['changestatusaplicacion', 'getstatusaplicacion', 'etiquetasarray'],
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
                        'actions' => ['responderpreguntas', 'responderpreguntasarchivo', 'set', 'uploadlogo', 'getallbyemprendedor', 'getbyemprendedor'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return Yii::$app->user->identity->isEmprendedor();
                            }

                            return false;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionAddintegrante()
    {
        $usuario = new Usuario();
        $emprendedor = new Emprendedor();
        $concursoAplicado = ConcursoAplicado::getConcursoAplicado(Yii::$app->session->get('id_proyecto'), Yii::$app->session->get('id_concurso'));

        if ($concursoAplicado == null) {
            Yii::$app->session->setFlash('resultIntegrante', [
                'target' => 'aIntegrantes',
                'message' => 'El concurso no se encuenta disponible',
                'postIntegrante' => Yii::$app->request->post(),
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        if (!$concursoAplicado->concurso->isValidFechaRegistro()) {
            Yii::$app->session->setFlash('result', [
                'target' => 'aIntegrantes',
                'message' => 'No tiene permitido registrar sus datos, el periodo de registro para el concurso es del '.Functions::transformDate($concursoAplicado->concurso->fecha_arranque, 'd-m-Y').' al '.Functions::transformDate($concursoAplicado->concurso->fecha_cierre, 'd-m-Y'),
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        $documentos = $concursoAplicado->checklistDocumentos;

        if ($documentos != null) {
            // Revisa si el perfil es editable
            if ($documentos->perfil_editable != 1) {
                Yii::$app->session->setFlash('result', [
                    'target' => 'aIntegrantes',
                    'message' => 'El periodo para editar su perfil ha finalizado, por lo que no puede hacer cambios a su información',
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);

                return $this->redirect(['emprendedor/perfil']);
            }
        }

        $checkMaxSocios = $concursoAplicado->concurso->validMaxSocios(count($concursoAplicado->proyecto->emprendedoresXProyecto));

        if ($checkMaxSocios !== true) {
            return $this->redirect(['emprendedor/perfil']);
        }

        if (Yii::$app->request->post('Usuario')) {
            $usuario->load(Yii::$app->request->post());
            $emprendedor->load(Yii::$app->request->post());

            $usuario->tipo = 6;
            $usuario->password = Security::encode(Security::createPassword());
            $usuario->password_repeat = $usuario->password;
            $usuario->auth_token = Security::createToken();
            $usuario->activo = 0;

            if (Model::validateMultiple([$usuario, $emprendedor])) {
                $transaction = Yii::$app->db->beginTransaction();

                $emprendedor->fecha_nacimiento = Functions::transformDate($emprendedor->fecha_nacimiento, 'Y-m-d');
                $usuario->save(false);
                $emprendedor->id_usuario = $usuario->id;
                $emprendedor->save(false);

                $integrante = new EmprendedorXProyecto();
                $integrante->id_emprendedor = $usuario->id;
                $integrante->id_proyecto = Yii::$app->session->get('id_proyecto');
                $integrante->save();

                $transaction->commit();

                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'Integrante registrado exitosamente',
                ]);
            } else {
                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($usuario->errors + $emprendedor->errors).'</ul>',
                    'postIntegrante' => Yii::$app->request->post(),
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);
            }
        }

        return $this->redirect(['emprendedor/perfil']);
    }

    public function actionUpdateintegrante()
    {
        $concursoAplicado = ConcursoAplicado::getConcursoAplicado(Yii::$app->session->get('id_proyecto'), Yii::$app->session->get('id_concurso'));

        if ($concursoAplicado == null) {
            Yii::$app->session->setFlash('resultIntegrante', [
                'target' => 'aIntegrantes',
                'message' => 'El concurso no se encuenta disponible',
                'postIntegrante' => Yii::$app->request->post(),
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        if (!$concursoAplicado->concurso->isValidFechaRegistro()) {
            Yii::$app->session->setFlash('result', [
                'target' => 'aIntegrantes',
                'message' => 'No tiene permitido registrar sus datos, el periodo de registro para el concurso es del '.Functions::transformDate($concursoAplicado->concurso->fecha_arranque, 'd-m-Y').' al '.Functions::transformDate($concursoAplicado->concurso->fecha_cierre, 'd-m-Y'),
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        $documentos = $concursoAplicado->checklistDocumentos;

        if ($documentos != null) {
            // Revisa si el perfil es editable
            if ($documentos->perfil_editable != 1) {
                Yii::$app->session->setFlash('result', [
                    'target' => 'aIntegrantes',
                    'message' => 'El periodo para editar su perfil ha finalizado, por lo que no puede hacer cambios a su información',
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);

                return $this->redirect(['emprendedor/perfil']);
            }
        }

        if (Yii::$app->request->post('Usuario')) {
            $usuario = Usuario::find()
                ->where('email = :email', [':email' => Yii::$app->request->post('Usuario')['email']])
                ->andWhere('tipo = 6')
                ->one();

            if ($usuario == null) {
                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'Integrante no disponible',
                    'postIntegrante' => Yii::$app->request->post(),
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);

                return $this->redirect(['emprendedor/perfil']);
            }

            $proyecto = Proyecto::findOne(Yii::$app->session->get('id_proyecto'));

            if ($proyecto == null) {
                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'Proyecto no disponible',
                    'postIntegrante' => Yii::$app->request->post(),
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);

                return $this->redirect(['emprendedor/perfil']);
            }

            if ($proyecto->validIntegrante($usuario->email) == false) {
                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'El Integrante seleccionado no forma parte del equipo del proyecto',
                    'postIntegrante' => Yii::$app->request->post(),
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);

                return $this->redirect(['emprendedor/perfil']);
            }

            $emprendedor = $usuario->emprendedor;

            if ($emprendedor == null) {
                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'Integrante no disponible',
                    'postIntegrante' => Yii::$app->request->post(),
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);

                return $this->redirect(['emprendedor/perfil']);
            }

            $usuario->load(Yii::$app->request->post());
            $emprendedor->load(Yii::$app->request->post());

            if (Model::validateMultiple([$usuario, $emprendedor])) {
                $emprendedor->fecha_nacimiento = Functions::transformDate($emprendedor->fecha_nacimiento, 'Y-m-d');
                $usuario->save(false);
                $emprendedor->save(false);

                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'Se actualizó exitosamente los datos del Integrante',
                ]);
            } else {
                Yii::$app->session->setFlash('resultIntegrante', [
                    'target' => 'aIntegrantes',
                    'message' => 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($usuario->errors + $emprendedor->errors).'</ul>',
                    'postIntegrante' => Yii::$app->request->post(),
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);
            }
        }

        return $this->redirect(['emprendedor/perfil']);
    }

    public function actionGetintegrante()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // Permite Cross Domain Requests
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');
        // Campos a excluir en la respuesta
        $excludeFields = ['tipo', 'password', 'auth_token', 'activo', 'id_usuario', 'id'];

        $datos = [];
        $usuario = Usuario::findOne(Yii::$app->request->post('id'));
        $proyecto = Proyecto::findOne(Yii::$app->session->get('id_proyecto'));

        // Se actualiza el sessionFlash de lo contrario se pierde
        // porque solo se mantiene en el siguiente request
        // esta variable se establece en EmprendedorController -> actionPerfil
        //Yii::$app->session->setFlash('id_proyecto', Yii::$app->session->getFlash('id_proyecto'));

        if ($proyecto == null) {
            return [
                'error' => true,
                'message' => 'Proyecto no disponible'
            ];
        }

        if ($usuario == null) {
            return [
                'error' => true,
                'message' => 'Usuario no disponible'
            ];
        }

        if ($proyecto->validIntegrante($usuario->email) == false) {
            return [
                'error' => true,
                'message' => 'El Integrante seleccionado no forma parte del equipo del proyecto'
            ];
        }

        $emprendedor = $usuario->emprendedor;

        foreach ($usuario->attributes as $key => $value) {
            if (!in_array($key, $excludeFields)) {
                $datos['Usuario['.$key.']'] = $value;
            }
        }

        if ($emprendedor != null) {
            foreach ($emprendedor->attributes as $key => $value) {
                if (!in_array($key, $excludeFields)) {
                    $datos['Emprendedor['.$key.']'] = $value;
                }
            }
        }

        return $datos;
    }

    public function actionChangestatusaplicacion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // Permite Cross Domain Requests
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');

        $result = [
            'error' => false,
            'message' => 'Status de la aplicación actualizado exitosamente'
        ];

        $chkDocumentos = ChecklistDocumentos::find()
            ->where('id_proyecto = :id_proyecto', [':id_proyecto' => Yii::$app->request->post('proyecto')])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => Yii::$app->request->post('concurso')])
            ->one();

        if ($chkDocumentos == null) {
            $result['error'] = true;
            $result['message'] = 'Aplicación del proyecto no disponible';
        } else {
            $chkDocumentos->aplicacion_aprobada = Yii::$app->request->post('aplicacionAprobada');
            $chkDocumentos->motivo_rechazo_aplicacion = Yii::$app->request->post('motivoRechazo') ? Yii::$app->request->post('motivoRechazo') : 'Motivo de rechazo no especificado';

            if (!$chkDocumentos->save()) {
                $result['error'] = true;
                $result['message'] = Functions::errorsToString($chkDocumentos->errors);
            }
        }

        return $result;
    }

    public function actionGetstatusaplicacion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // Permite Cross Domain Requests
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');

        $datos = [
            'error' => false
        ];

        $chkDocumentos = ChecklistDocumentos::find()
            ->where('id_proyecto = :id_proyecto', [':id_proyecto' => Yii::$app->request->post('proyecto')])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => Yii::$app->request->post('concurso')])
            ->one();

        if ($chkDocumentos == null) {
            $datos['error'] = true;
            $datos['message'] = 'Aplicación del proyecto no disponible';
        } else {
            $datos['aplicacion_aprobada'] = $chkDocumentos->aplicacion_aprobada;
            $datos['motivo_rechazo_aplicacion'] = $chkDocumentos->motivo_rechazo_aplicacion;
        }

        return $datos;
    }

    public function actionUploadplannegocios()
    {
        $chkDocumentos = ChecklistDocumentos::getChecklistDocumentos(Yii::$app->session->get('id_proyecto'), Yii::$app->session->get('id_concurso'));

        if ($chkDocumentos == null) {
            Yii::$app->session->setFlash('result', [
                'target' => 'aMiProyecto',
                'message' => 'El Proyecto no se encuentra asociado al Concurso actual',
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        if (!$chkDocumentos->concurso->isValidFechaEnvioPlan()) {
             Yii::$app->session->setFlash('result', [
                'target' => 'aMiProyecto',
                'message' => 'La fecha límite para la entrega del plan de negocios fué el '.Functions::transformDate($chkDocumentos->concurso->fecha_limite_envio_plan, 'd-m-Y'),
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        if ($chkDocumentos->puede_enviar_plan != 1) {
             Yii::$app->session->setFlash('result', [
                'target' => 'aMiProyecto',
                'message' => 'No tiene permitido enviar el Plan de Negocios',
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        if ($chkDocumentos->plan_entregado == 1) {
            Yii::$app->session->setFlash('result', [
                'target' => 'aMiProyecto',
                'message' => 'Ya subió su Plan de Negocios',
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        $chkDocumentos->load(Yii::$app->request->post());
        $chkDocumentos->doc_plan_negocios = UploadedFile::getInstance($chkDocumentos, 'doc_plan_negocios');

        if ($chkDocumentos->doc_plan_negocios == null) {
            Yii::$app->session->setFlash('result', [
                'target' => 'aMiProyecto',
                'message' => 'No se puede obtener el archivo seleccionado',
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);

            return $this->redirect(['emprendedor/perfil']);
        }

        if ($chkDocumentos->validate()) {
            if ($chkDocumentos->doc_plan_negocios->hasError) {
                Yii::$app->session->setFlash('result', [
                    'target' => 'aMiProyecto',
                    'message' => 'Error al subir el archivo: '.$chkDocumentos->doc_plan_negocios->error,
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);
            } else {
                $chkDocumentos->puede_enviar_plan = 0;
                $chkDocumentos->plan_entregado = 1;
                // Primero debemos llamar a save del modelo
                // despues a save del documento
                // http://stackoverflow.com/questions/26998914/yii-framework-2-0-uploading-files-error-finfo-file-failed-to-open-stream-no
                $chkDocumentos->save();
                // Se elimina cualquier otro archivo que existe de este proyecto
                $chkDocumentos->proyecto->deletePlanNegocios();
                // Subimos el archivo actual
                $chkDocumentos->uploadPlanNegocios();


                Yii::$app->session->setFlash('result', [
                    'target' => 'aMiProyecto',
                    'message' => 'Datos actualizados correctamente',
                ]);
            }
        } else {
            Yii::$app->session->setFlash('result', [
                'target' => 'aMiProyecto',
                'message' => 'Corriga los siguientes errores: <ul>'.Functions::errorsToList($chkDocumentos->errors).'</ul>',
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times',
            ]);
        }

        return $this->redirect(['emprendedor/perfil']);
    }

    public function actionDownloadplannegocios()
    {
        $proyecto = Proyecto::findOne(Yii::$app->request->post('proyecto'));

        if ($proyecto == null) {
            $proyecto = Proyecto::findOne(Yii::$app->session->get('id_proyecto'));
        }

        if ($proyecto == null) {
            Yii::$app->response->statusCode = 500;
            Yii::$app->response->content = 'Proyecto no encontrado';
            return Yii::$app->response;
        }

        if ($proyecto->planNegocios == null) {
            Yii::$app->response->statusCode = 500;
            Yii::$app->response->content = 'Plan de Negocios no encontrado';
            return Yii::$app->response;
        }

        return Yii::$app->response->sendFile($proyecto->downloadPlanNegocios(), $proyecto->nombrePlanNegocios);
    }

    public function actionSet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $badgeU = BadgeXUsuario::findOne(['id_usuario' => Yii::$app->user->identity->id, 'id_badge' => Badge::$SOY_YO]);

        if(empty($badgeU))
        {
          //Yii::$app->session->setFlash('alert', 'Te invitamos a que completes los datos de tu perfil en la sección MI INFO, mientras más completo tengas tus datos, mejores oportunidades tendrás de ser seleccionado en los concursos que se gestionarán en esta plataforma.');
          $result = ['error' => true, 'message' => 'Para registrar tu proyecto necesitas completar los datos de tu perfil. Te invitamos a completarlos en la sección MI INFO ubicada en la parte superfior derecha.', 'id' => null, 'imagen' => null];
          return $result;
        }
        else
        {
        $transaction = Yii::$app->db->beginTransaction();
        $result = ['error' => false, 'message' => 'datos guardados exitosamente', 'id' => null, 'imagen' => null];

        try {
            $proyecto = Proyecto::findOne(Yii::$app->request->post('proyecto'));

            if ($proyecto == null) {
                $proyecto = new Proyecto();
            } else if (!$proyecto->isCreadorOrIntegrante(Yii::$app->user->identity->id) ) {
                throw new \yii\web\UnauthorizedHttpException('No tiene autorizado acceder al proyecto.');
            }

            $proyecto->load(['Proyecto' => Yii::$app->request->post()]);

            if (empty($proyecto->id_emprendedor_creador)) {
                $proyecto->id_emprendedor_creador = Yii::$app->user->identity->id;
            }

              $proyecto->save();


            if ($proyecto->errors) {
                $result['error'] = true;
                $result['message'] = json_encode($proyecto->errors);
                return $result;
                //throw new \yii\web\BadRequestHttpException(json_encode($proyecto->errors));
            }

            $result['id'] = $proyecto->id;

            EtiquetasXProyecto::deleteAll(['id_proyecto' => $proyecto->id]);

            if (Yii::$app->request->post('etiquetas')) {
                foreach (Yii::$app->request->post('etiquetas') as $id_etiqueta) {
                    $etiqueta = EtiquetasXProyecto::find()
                        ->where(['id_proyecto' => $proyecto->id])
                        ->andWhere(['id_etiqueta' => $id_etiqueta])
                        ->one();

                    if ($etiqueta == null) {
                        $etiqueta = new EtiquetasXProyecto();
                        $etiqueta->id_proyecto = $proyecto->id;
                        $etiqueta->id_etiqueta = $id_etiqueta;
                        $etiqueta->save();
                    }
                }
            }

            EmprendedorXProyecto::deleteAll(['id_proyecto' => $proyecto->id]);

            $list_integrantes = Yii::$app->request->post('list_integrantes');

            if ($list_integrantes) {
                $list_integrantes = array_filter($list_integrantes);

                foreach ($list_integrantes as $id_integrante) {
                    $emprendedorProyecto = new EmprendedorXProyecto();
                    $emprendedorProyecto->id_proyecto = $proyecto->id;
                    $emprendedorProyecto->id_emprendedor = $id_integrante;
                    $emprendedorProyecto->save();
                }
            }

            $pathImagen = 'proyecto'.DIRECTORY_SEPARATOR.$proyecto->id.'_imagen.jpg';

            if (Functions::uploadFile('imagen', $pathImagen)) {
                $type = pathinfo($proyecto->pathImagen, PATHINFO_EXTENSION);
                $imageByte = file_get_contents($proyecto->pathImagen);
                $base64Foto = 'data:image/' . $type . ';base64,' . base64_encode($imageByte);
                $result['byteImagen'] = $base64Foto;
            }

            if (!empty($proyecto->url_video)) {
                // Checamos si el usuario todavia no tiene el badge
                $badge = BadgeXUsuario::findOne(['id_usuario' => Yii::$app->user->identity->id, 'id_badge' => Badge::$CAMARA_ACCION]);

                if (empty($badge)) {
                    $badge = new BadgeXUsuario();
                    $badge->id_usuario = Yii::$app->user->identity->id;
                    $badge->id_badge = Badge::$CAMARA_ACCION;
                    $badge->save();
                }
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $result['error'] = true;
            $result['message'] = json_encode(['DatosGenerales' => $e->getMessage()]);
            //throw new \yii\web\BadRequestHttpException($e->getMessage().' --- '. $e->getLine().' '.$e->getFile().' --- '.$e->getTraceAsString());
        }
      }
      return $result;
    }

    public function actionUploadlogo()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $result = ['error' => false, 'message' => 'datos guardados exitosamente'];

        try {
            Functions::uploadFile('logo', 'proyecto/'.Yii::$app->request->post('id').'_logo.jpg');
        } catch(\Exception $e) {
            $result['error'] = true;
            $result['message'] = json_encode(['logo' => $e->getMessage()]);
            //throw new \yii\web\BadRequestHttpException($e->getMessage());
        }

        return $result;
    }

    public function actionResponderpreguntas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $result = [
            'error' => false,
            'message' => 'Respuestas guardadas exitosamente',
        ];

        $list_respuestas = Yii::$app->request->post('list_respuestas');
        $id_proyecto = Yii::$app->request->post('id_proyecto');

        if (count($list_respuestas)) {
            foreach($list_respuestas as $respuesta) {
                $valRespuesta = $respuesta['valor'];

                if (is_array($valRespuesta)) {
                    $valRespuesta = json_encode($valRespuesta);
                }

                if (empty($valRespuesta)) {
                    $result['error'] = true;
                    $result['message'] = 'Debe especificar una respuesta válida';
                    $result['id_pregunta'] = $respuesta['id_pregunta'];

                    return $result;
                }

                $pregunta = Pregunta::findOne($respuesta['id_pregunta']);

                if ($pregunta == null) {
                    $result['error'] = true;
                    $result['message'] = 'Pregunta no disponible';
                    $result['id_pregunta'] = $respuesta['id_pregunta'];

                    return $result;
                }

                $respuesta = $pregunta->getRespuesta($id_proyecto);

                if ($respuesta == null) {
                    $respuesta = new Respuesta();
                    $respuesta->id_pregunta = $pregunta->id;
                    $respuesta->id_proyecto = $id_proyecto;
                }

                $respuesta->setAttribute($pregunta->tipoPregunta->columna_respuesta, $valRespuesta);
                $respuesta->ponderacion = $pregunta->getPonderacionRespuesta($valRespuesta);
                $respuesta->fecha_edicion = date('Y-m-d H:i:s');

                if (!$respuesta->save()) {
                    $result['error'] = true;
                    $result['message'] = 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($respuesta->errors).'</ul>';
                    $result['id_pregunta'] = $respuesta['id_pregunta'];
                }
            }
        }

        if (Badge::checkBadgeListoAplicar($id_proyecto)) {
            // Checamos si el usuario todavia no tiene el badge
            $badge = BadgeXUsuario::findOne(['id_usuario' => Yii::$app->user->identity->id, 'id_badge' => Badge::$LISTO_APLICAR]);

            if (empty($badge)) {
                $badge = new BadgeXUsuario();
                $badge->id_usuario = Yii::$app->user->identity->id;
                $badge->id_badge = Badge::$LISTO_APLICAR;
                $badge->save();
            }
        }

        return $result;
    }

    public function actionResponderpreguntasarchivo()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Archivo subido exitosamente',
        ];

        $id_pregunta = Yii::$app->request->post('pregunta');
        $proyecto = Yii::$app->request->post('proyecto');
        $concurso = Yii::$app->request->post('concurso');
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Validamos que el proyecto pertenesca al usuario logueado
            if (Proyecto::getByEmprendedor(Yii::$app->user->identity->id, $proyecto) == null) {
                $result['error'] = true;
                $result['message'] = 'El proyecto no le pertenece';
            }/* else if (ConcursoAplicado::find()->where('id_concurso='.(int)$concurso.' AND id_proyecto='.(int)$proyecto)->one() == null) {
                $result['error'] = true;
                $result['message'] = 'El proyecto no esta inscrito al concurso seleccionado';
            }*/ else {
                $pregunta = PreguntaXConcurso::findOne($id_pregunta);

                if ($pregunta == null) {
                    $result['error'] = true;
                    $result['message'] = 'Pregunta no disponible';

                    return $result;
                }

                $respuesta = new RespuestaConcurso();
                $respuesta->id_concurso = $concurso;
                $respuesta->id_proyecto = $proyecto;
                $respuesta->id_pregunta = $pregunta->id;
                $respuesta->solo_concurso = 1;
                $respuesta->ponderacion = 1;

                if (!$respuesta->save()) {
                    $result['error'] = true;
                    $result['message'] = 'Por favor corrija los siguientes errores:<ul>'.Functions::errorsToList($respuesta->errors).'</ul>';
                }

                Functions::uploadFile('archivo', 'respuestaarchivo/'.$concurso.'_'.$proyecto.'/'.$respuesta->id, true);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $result['error'] = true;
            $result['message'] = $e->getMessage();
            $result['trace'] = $e->getTraceAsString();
        }

        return $result;
    }

    public function actionGetallbyemprendedor()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $proyectos = Proyecto::getAllByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->get('page'));

        if (Yii::$app->request->get('compact')) {
            $arrProyectos = ArrayHelper::toArray($proyectos, [
                                'app\models\Proyecto' => [
                                    'id',
                                    'nombre',
                                ]]);
        } else {
            $arrProyectos = ArrayHelper::toArray($proyectos, [
                                'app\models\Proyecto' => [
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'byteimagen',
                                    'etiquetas',
                                ],
                                'app\models\Etiqueta' => [
                                    'descripcion',
                                ]
                            ]);
        }

        return [
            'total' => count($arrProyectos),
            'result' => $arrProyectos
        ];
    }

    public function actionGetbyemprendedor()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $proyecto = Proyecto::getByEmprendedor(Yii::$app->user->identity->id, Yii::$app->request->post('id'))
                        ->toArray(
                            ['id', 'nombre', 'descripcion', 'url_video', 'integrantes'], // Solo exportamos algunos atributos
                            ['etiquetas', 'emprendedoresXProyecto', 'bytelogo', 'byteimagen', 'noParticipacion', 'porcentajeCompletado'] // Se incluyen algunos campos extras en el arreglo
                        );

        if (Yii::$app->request->post('includeExtras') && !empty($proyecto)) {
            $emprendedores = [];

            foreach($proyecto['emprendedoresXProyecto'] as $emprendedor) {
                $usuario = Usuario::findOne($emprendedor['id_emprendedor']);

                if ($usuario) {
                    $emprendedores[] = [
                        'id' => $usuario->id,
                        'nombre' => $usuario->nombre_completo
                    ];
                }
            }

            unset($proyecto['emprendedoresXProyecto']);
            $proyecto['emprendedores'] = $emprendedores;

            $proyecto['respuestas'] = ArrayHelper::toArray(Respuesta::getByProyecto($proyecto['id']), [
                    'app\models\Respuesta' => ['id_pregunta', 'respuesta_numerica', 'respuesta_texto', 'respuesta_opcion', 'respuesta_geografica']
                ]);
        }

        return $proyecto;
    }

    /**
     *
     * @return mixed
     */
    public function actionMicrositio($p)
    {
        $proyecto = Proyecto::find()->with('ganadores.idConcurso.institucion')->where('id = '.$p)->one();
        $secciones = Seccion::find()->all();

        //Obtiene los concursos que ganó el proyecto
        $ganadores = Ganadores::find()
            ->where('id_proyecto = :id_proyecto', [':id_proyecto' => $p])
            ->with('idConcurso')
            ->all();

        return $this->render('micrositio', [
            'proyecto' => $proyecto,
            'secciones' => $secciones,
            'ganados' => $ganadores
        ]);
    }

    public function actionDownloadrespuestaarchivo()
    {
        $concurso = Concurso::findOne(Yii::$app->request->post('concurso'));
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $headers = Yii::$app->response->headers;

        if ($concurso == null || $concurso->id_institucion != Yii::$app->user->identity->institucion->id) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = 'Concurso no encontrado';
            $headers->add('Content-Type', 'text/xml; charset=utf-8');
            return Yii::$app->response;
        }

        $respuesta = RespuestaConcurso::find()
                ->where(['id_concurso' => Yii::$app->request->post('concurso'),
                    'id_proyecto' => Yii::$app->request->post('proyecto'),
                    'id_pregunta' => Yii::$app->request->post('pregunta'),
                    'solo_concurso' => '1'
                ])->one();

        if (empty($respuesta)) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = 'Respuesta no encontrada';
            $headers->add('Content-Type', 'text/xml; charset=utf-8');
            return Yii::$app->response;
        }

        if ($respuesta->downloadArchivoRespuesta() == null) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->content = 'Archivo no encontrado';
            $headers->add('Content-Type', 'text/xml; charset=utf-8');
            return Yii::$app->response;
        }

        return Yii::$app->response->sendFile($respuesta->downloadArchivoRespuesta(), $respuesta->getNombreArchivo());
    }

    public function actionSetstatusaplicacion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'Datos guardados exitosamente',
        ];

        try {
            $concurso = Concurso::findOne(Yii::$app->request->post('concurso'));

            if ($concurso == null || $concurso->id_institucion != Yii::$app->user->identity->institucion->id) {
                throw new \Exception('Concurso no disponible.');
            }

            $concursoAplicado = ConcursoAplicado::getConcursoAplicado(Yii::$app->request->post('proyecto'), $concurso->id);

            if ($concursoAplicado == null) {
                throw new \Exception('El proyecto no aplico al concurso.');
            }

            $concursoAplicado->paso_filtros = Yii::$app->request->post('status');

            if ($concursoAplicado->paso_filtros == 0) {
                $concursoAplicado->filtros_no_pasados = Concurso::crearFiltroNoPasado(0, "El Proyecto NO cumple con los requerimientos del Concurso.");
            }

            if(!$concursoAplicado->save()) {
                throw new \Exception('No se puede establecer el status de aprobado.');
            }

        } catch (\Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }
    
    public function actionEtiquetasarray(){
        $proyectos = Proyecto::find()->with('etiquetas')->all();
        foreach($proyectos as $proyecto){
            echo $proyecto->id." ".$proyecto->nombre;
            echo ".....................<br>";
            //$i = 0;
            $etiquetas = "[";
            $etiquetasLeft = sizeof($proyecto->etiquetas);
            foreach($proyecto->etiquetas as $etiqueta)
            {
                //echo "etiqueta: ".$etiqueta->id." fin. <br>";
                $etiquetas .= $etiqueta->id;
                $etiquetasLeft--;
                
                if( $etiquetasLeft > 0 ) $etiquetas.= ", ";
            }
            $etiquetas .= "]";
            echo $etiquetas. "<br><br>";
            $proy = Proyecto::findOne($proyecto->id);
            $proy->etiquetas_array = $etiquetas;
            $proy->save();
        }
    }
}
