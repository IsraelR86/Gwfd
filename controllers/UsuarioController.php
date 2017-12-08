<?php

namespace app\controllers;

use Yii;
use app\models\Usuario;
use app\models\UsuarioSearch;
use app\models\Estado;
use app\models\Universidad;
use app\models\Ciudad;
use app\models\SingupForm;
use app\models\Badge;
use app\models\BadgeXUsuario;
use app\helpers\Security;
use app\helpers\Functions;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * UsuarioController implements the CRUD actions for Usuario model.
 */
class UsuarioController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Usuario';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Usuarios';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'changepass'],
                'rules' => [
                    [
                        'actions' => ['changepass'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'view', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return true; // Temporal
                            if (Yii::$app->user->identity) {
                                return Yii::$app->user->identity->isAdministrador();
                            }

                            return false;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Valida al Usuario con el link enviado por correo
     * Se utiliza el auth_token para validar
     *
     * @param string $token
     * @return mixed
     */
    public function actionValidar($token)
    {
        $model = Usuario::find()
            ->where('auth_token = :token', [':token' => $token])
            ->andWhere('activo = 0')
            ->one();

        if ($model == null) {
            Yii::$app->session->setFlash('result', [
                'target' => 'login-box',
                'message' => 'No se puede validar la cuenta, es posible que ya este validada o que el correo electrónico proporcionado no este previamente registrado',
                'alert-class' => 'alert-danger',
                'alert-icon' => 'times red',
            ]);
        } else {
            $model->activo = 1;
            $model->set_activo();

            Yii::$app->session->setFlash('result', [
                'target' => 'login-box',
                'message' => 'Se ha válidado exitosamente la cuenta, ahora inicie sesión para completar sus datos personales'
            ]);
        }

        return Yii::$app->getResponse()->redirect(['site/index']);
    }

    public function actionChangepass()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'message' => 'La contraseña se actualizó exitosamente',
        ];

        if (Yii::$app->request->post('new_pass')) {
            $usuario = Yii::$app->user->identity;
            $usuario->scenario = 'update';
            $usuario->password = Security::encode(Yii::$app->request->post('new_pass'));

            if (!$usuario->save()) {
                $result['error'] = true;
                $result['message'] = 'Ocurrió un error al actualizar la contraseña, intentelo nuevamente';
            }

            /*Yii::$app->session->setFlash('result', [
                'target' => 'aMiPerfil',
                'message' => 'Se ha cambiado la contraseña exitosamente'
            ]);*/
        }

        return $result;
        // return $this->goBack();
    }

    /**
     * Lists all Usuario models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuarioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Displays a single Usuario model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Solo se puede visualizar a usuario Administrador o Evaluador
        if (!$model->isAdministrador() && !$model->isEvaluador()) {
            return $this->redirect(['index']);
        }

        return $this->render('view', [
            'model' => $model,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Creates a new Usuario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($tipo)
    {
        $model = new Usuario();

        if (Yii::$app->request->isPost) {
            $model->auth_token = Security::createToken();
            #$model->activo = 0;
            $model->tipo = $tipo; //Yii::$app->session->getFlash('tipo_usuario');
            $model->password = "123456";
            // Se establece el escenario para aplicar las reglas de validación correspondientes al registro de usuario
            $model->setScenario('register');

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('result', [
                    'message' => 'Datos registrados exitosamente',
                ]);
                /*Yii::$app->mailer->compose()
                    ->setFrom([Yii::$app->params['email_usr'] => Yii::$app->params['email_name']])
                    ->setTo($model->email)
                    ->setSubject(Yii::$app->params['title'] . ': Confirmación de cuenta')
                    ->setHtmlBody(str_replace('{url}', Yii::$app->urlManager->createAbsoluteUrl(['usuario/validar', 'token'=>$model->auth_token]), Yii::$app->params['email_confirmacion']).
                        '<p>Su contraseña para acceder a la plataforma es: <strong>'.$password.'</strong></p>')
                    ->send();*/

                error_log($password.' - '.Yii::$app->urlManager->createAbsoluteUrl(['usuario/validar', 'token'=>$model->auth_token]));
            } else {
                Yii::$app->session->setFlash('result', [
                    'message' => 'Ocurrió un error al registrar los datos: '.Functions::errorsToString($model->errors),
                    'alert-class' => 'alert-danger',
                    'alert-icon' => 'times',
                ]);
            }
        }
        //Yii::$app->session->setFlash('tipo_usuario', Usuario::$EMPRENDEDOR);
        //Yii::$app->session->setFlash('action_form', Url::toRoute('usuario/create'));
        // Se establece el escenario para aplicar las reglas de validación correspondientes al registro de usuario
        $model->setScenario('register');

        /* Solo muestra el formulario, el controlador encargado de hacer el insert es el Usuario/create */
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Usuario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $universidades = ArrayHelper::map(
            Universidad::find()
            ->select('id, nombre')
            ->where('activo = 1')
            ->orderBy('nombre')
            ->all(),
            'id', 'nombre'
        );

        // Solo se puede editar a usuario Administrador o Evaluador
        if (!$model->isAdministrador() && !$model->isEvaluador()) {
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'universidades' => $universidades,
            ]);
        }
    }

    /**
     * Deletes an existing Usuario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the Usuario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuario::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('El usuario solicitado no existe.');
        }
    }

    /**
     *
     */
    public function actionGetperfil()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $perfil = [];

        if (Yii::$app->user->identity != null) {
            switch(Yii::$app->user->identity->tipo) {
                case Usuario::$ADMINISTRADOR;
                        $extras = [];
                    break;
                case Usuario::$EMPRENDEDOR;
                        $extras = Yii::$app->user->identity->emprendedor->getAttributes();
                        $extras['avatar'] = Yii::$app->user->identity->getByteimagen();
                    break;
                case Usuario::$EVALUADOR;
                        $extras = Yii::$app->user->identity->evaluador->getAttributes();
                        $extras['avatar'] = Yii::$app->user->identity->getByteimagen();
                    break;
                case Usuario::$INSTITUCION   = 4;
                        //$extras = [];

                        //
                        $extras = Yii::$app->user->identity->institucion->getAttributes();
                        $extras['avatar'] = Yii::$app->user->identity->getByteimagen();
                        //
                    break;
            }

            $perfil = Yii::$app->user->identity->getAttributes();
            $perfil['extras'] = $extras;

            if (isset($perfil['password'])) {
                unset($perfil['password']);
            }

            if (isset($perfil['auth_token'])) {
                unset($perfil['auth_token']);
            }

            if (isset($perfil['tipo'])) {
                unset($perfil['tipo']);
            }

            if (isset($perfil['extras']['id_usuario'])) {
                unset($perfil['extras']['id_usuario']);
            }

            if (isset($perfil['extras']['fecha_nacimiento'])) {
                $perfil['extras']['fecha_nacimiento'] = Functions::transformDate($perfil['extras']['fecha_nacimiento'], 'd-m-Y');
            }
        }

        return $perfil;
    }

    /**
     *
     */
    public function actionSetperfil()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $result = ['error' => false, 'message' => 'Datos Guardados Exitosamente'];
        $usuario = Yii::$app->user->identity;
        $extras = null;

        $params = [];

       if (count(Yii::$app->request->bodyParams)) {
           $params = Yii::$app->request->bodyParams;
       } else {
           $params = Yii::$app->request->post();
       }

        if (Yii::$app->user->identity != null) {
            switch(Yii::$app->user->identity->tipo) {
                case Usuario::$ADMINISTRADOR;
                        $extras = null;
                    break;
                case Usuario::$EMPRENDEDOR;
                        $extras = Yii::$app->user->identity->emprendedor;
                        $datos_extras = ['Emprendedor' => $params['extras']];
                    break;
                case Usuario::$EVALUADOR;
                        $extras = Yii::$app->user->identity->evaluador;
                        $datos_extras = ['Evaluador' => $params['extras']];
                    break;
                case Usuario::$INSTITUCION   = 4;
                        $extras = Yii::$app->user->identity->institucion;
                        $datos_extras = ['Institucion' => $params['extras']];
                    break;
            }

            Functions::uploadFile('avatar', 'usuario/'.Yii::$app->user->identity->id.'.jpg');

            $datos = ['Usuario' => Yii::$app->request->post()];

            if ($usuario->load($datos) && $usuario->save()) {
                if ($extras) {
                    if (isset($datos_extras['Emprendedor'])) {
                        $datos_extras['Emprendedor']['fecha_nacimiento'] = Functions::transformDate($datos_extras['Emprendedor']['fecha_nacimiento']);
                    }

                    if (isset($datos_extras['Evaluador'])) {
                        $datos_extras['Evaluador']['fecha_nacimiento'] = Functions::transformDate($datos_extras['Evaluador']['fecha_nacimiento']);
                    }

                    if (!$extras->load($datos_extras) || !$extras->save()) {
                        $result['error'] = true;
                        $result['message'] = Functions::errorsToString($extras->errors);
                        $result['attrs'] = array_keys($extras->errors);
                    } else if (Yii::$app->user->identity->tipo == Usuario::$EMPRENDEDOR) {
                      if (Badge::checkBadgeSoyYo($usuario)) {
                          // Checamos si el usuario todavia no tiene el badge
                          $badge = BadgeXUsuario::findOne(['id_usuario' => Yii::$app->user->identity->id, 'id_badge' => Badge::$SOY_YO]);

                          if (empty($badge)) {
                              BadgeXUsuario::deleteAll(['id_usuario' => Yii::$app->user->identity->id, 'id_badge' => Badge::$SOY_YO]);
                              $badge = new BadgeXUsuario();
                              $badge->id_usuario = Yii::$app->user->identity->id;
                              $badge->id_badge = Badge::$SOY_YO;
                              $badge->save();
                          }
                      }
                      else {
                        BadgeXUsuario::deleteAll(['id_usuario' => Yii::$app->user->identity->id, 'id_badge' => Badge::$SOY_YO]);
                      }
                  }
              }
            } else {
                $result['error'] = true;
                $result['message'] = Functions::errorsToString($usuario->errors);
                $result['attrs'] = array_keys($extras->errors);
            }
        }
        
        return $result;
    }

    public function actionSingup()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
        }

        $model = new SingupForm();

        return $this->render('singup', [
            
            'model' => $model,
        ]);
    }

    public function actionRecuperarpass()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();
        $result = [
            'error' => false,
            'message' => 'Se ha enviado un correo electrónico a la dirección proporcionada, revise su bandeja de entrada.'
        ];

        try {
            $params = Yii::$app->request->post('RecuperarPassForm');
            $usuario = $this->findModel(['email' => $params['email']]);
            $nuevo_pass = Yii::$app->getSecurity()->generateRandomString(8);
            $usuario->password = Security::encode($nuevo_pass);

            if (!$usuario->save()) {
                throw new \yii\web\ConflictHttpException(Functions::errorsToString($usuario->errors));
            } else {
                Yii::$app->mailer->compose('recuperar_password', [
                        'clave' => $nuevo_pass
                    ])
                    ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                    ->setTo($usuario->email)
                    ->setSubject(Yii::$app->params['title'] . ': Recuperar Contraseña')
                    ->send();
                $transaction->commit();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \yii\web\ConflictHttpException(htmlentities(utf8_encode($e->getMessage())));
        }

        return $result;
    }

  }
