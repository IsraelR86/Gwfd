<?php

namespace app\controllers;

use Yii;
use app\models\Emprendedor;
use app\models\EmprendedorSearch;
use app\models\Estado;
use app\models\Ciudad;
use app\models\Universidad;
use app\models\Usuario;
use app\models\Proyecto;
use app\models\EmprendedoresXProyectos;
use app\models\LoginForm;
use app\helpers\Security;
use app\helpers\Functions;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EmprendedorController implements the CRUD actions for Emprendedor model.
 */
class EmprendedorController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Emprendedor';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Emprendedores';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'aplica', 'misproyectos', 'misconcursos', 'perfil', 'ver'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return Yii::$app->user->identity->isEmprendedor();
                            }

                            return false;
                        },
                    ],
                    [
                        'actions' => ['listar', 'findbyname'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity) {
                                return (Yii::$app->user->identity->isEmprendedor() || Yii::$app->user->identity->isAdministrador());
                            }

                            return false;
                        },
                    ],
                    [
                        'actions' => ['login', 'signup', 'registrar'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Emprendedor models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect([Usuario::$HOME[Usuario::$EMPRENDEDOR]]);
        /*$searchModel = new EmprendedorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);*/
    }

    /**
     * Displays a single Emprendedor model.
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
     * Muestra el formulario de registro para el tipo de usuario emprendedor
     *
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new Usuario();
        Yii::$app->session->setFlash('tipo_usuario', Usuario::$EMPRENDEDOR);
        Yii::$app->session->setFlash('action_form', Url::toRoute('usuario/create'));
        // Se establece el escenario para aplicar las reglas de validación correspondientes al registro de usuario
        $model->setScenario('register');

        /* Solo muestra el formulario, el controlador encargado de hacer el insert es el Usuario/create */
        return $this->render('//usuario/_form', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Emprendedor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Emprendedor();
        $catalogs = EmprendedorController::getArrayCatalogs(Yii::$app->request->post('Emprendedor'));

        $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isPost && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_usuario]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
            ] + $catalogs);
        }
    }

    /**
     * Updates an existing Emprendedor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $catalogs = EmprendedorController::getArrayCatalogs(Yii::$app->request->post('Emprendedor'));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_usuario]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
            ] + $catalogs);
        }
    }

    /**
     * Deletes an existing Emprendedor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Emprendedor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Emprendedor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Emprendedor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('La página solicitada no existe.');
        }
    }

    /**
     * Obtiene un array con los catálogos necesarios
     * para el formularo de registro/edición
     */
    public static function getArrayCatalogs($params)
    {
        $catalogs = [];

        $catalogs['estados'] = ArrayHelper::map(
            Estado::find()
            ->select('id, descripcion')
            ->orderBy('descripcion')
            ->all(),
            'id', 'descripcion'
        );

        $catalogs['ciudades'] = ArrayHelper::map(
            Ciudad::find()
            ->select('id, descripcion')
            ->where('id_estado = '.(isset($params['id_estado']) ? $params['id_estado'] : 0))
            ->orderBy('descripcion')
            ->all(),
            'id', 'descripcion'
        );

        $catalogs['ciudadesNacimiento'] = ArrayHelper::map(
            Ciudad::find()
            ->select('id, descripcion')
            ->where('id_estado = '.(isset($params['id_estado_nacimiento']) ? $params['id_estado_nacimiento'] : 0))
            ->orderBy('descripcion')
            ->all(),
            'id', 'descripcion'
        );

        $catalogs['universidades'] = ArrayHelper::map(
            Universidad::find()
            ->select('id, nombre')
            ->where('activo = 1')
            ->orderBy('nombre')
            ->all(),
            'id', 'nombre'
        );

        return $catalogs;
    }

    public function actionAplica()
    {
        return $this->render('aplica');
    }

    public function actionMisproyectos()
    {
        return $this->render('misproyectos');
    }

    public function actionMisconcursos()
    {
        return $this->render('misconcursos');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Usuario::$EMPRENDEDOR]]);
        }

        $model = new LoginForm();
        $model->role = Usuario::$EMPRENDEDOR;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect([Usuario::$HOME[Usuario::$EMPRENDEDOR]]);
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

    public function actionListar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $emprendedores = ArrayHelper::toArray(Emprendedor::find()->all(), [
                                'app\models\Emprendedor' => [
                                    'usuario',
                                ],
                                'app\models\Usuario' => [
                                    'id',
                                    'nombre_completo',
                                ],
                            ]);

        return $emprendedores;
    }

    public function actionFindbyname()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $emprendedores = [];

        if (Yii::$app->request->get('term')) {
            $emprendedores = ArrayHelper::toArray(Emprendedor::findByName(Yii::$app->request->get('term'), true), [
                                'app\models\Usuario' => [
                                    'id',
                                    'nombre_completo',
                                ],
                            ]);
        }

        return $emprendedores;
    }

    public function actionVer($id)
    {
        $emprendedor = Emprendedor::find()
        ->with('usuario', 'emprendedoresXProyectos.proyecto.ganadores.idConcurso', 'ciudadNacimiento.estado')
        ->where('id_usuario = '.$id)
        ->one();


        return $this->render('ver', [
            'emprendedor' => $emprendedor
        ]);
    }

    public function beforeAction($action)
    {
        if ($action->id == 'registrar') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionRegistrar()
    {
        // Se deshabilita el chequedo de CSRF en el beforeAction
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $result = [
            'error' => false,
            'mensaje' => 'Registro exitoso',
        ];

        if (Yii::$app->request->isPost) {
            $usuario = new Usuario();
            $usuario->tipo = 2;
            $usuario->auth_token = Security::createToken();
            // Se establece el escenario para aplicar las reglas de validación correspondientes al registro de usuario
            $usuario->setScenario('signup');
            $usuario->load(['Usuario' => Yii::$app->request->post()]);
            $usuario->password = Security::encode($usuario->password);

            if ($usuario->save()) {
                $emprendedor = new Emprendedor();
                $emprendedor->setScenario('signup');
                $emprendedor->id_usuario = $usuario->id;
                $emprendedor->id_estado = 7;
                $usuario->fecha_registro = date('Y-m-d H:i:s');

                $emprendedor->load(['Emprendedor' => Yii::$app->request->post()]);

                if (!$emprendedor->save()) {
                    $result['error'] = true;
                    $result['mensaje'] = Functions::errorsToString($emprendedor->errors);
                } else {
                    // Enviar correo electrónico
                    Yii::$app->mailer->compose('nuevo_cliente', [])
                    ->setFrom([Yii::$app->params['mail_username'] => Yii::$app->params['title']])
                    ->setTo($usuario->email)
                    ->setSubject(Yii::$app->params['title'] . ': Bienvenido')
                    ->send();
                }
            } else {
                $result['error'] = true;
                $result['mensaje'] = Functions::errorsToString($usuario->errors);
            }
        } else {
            $result['error'] = true;
            $result['mensaje'] = 'La solicitud tiene que realizarse con el metodo POST';
        }

        return $result;
    }
}
