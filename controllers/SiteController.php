<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Concurso;
use app\models\Usuario;
use app\models\Badge;
use app\models\RecuperarPassForm;
use app\models\LogEvento;
use app\helpers\Security;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{
    public $successUrl = 'Success';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }

    public function successCallback($client)
    {
        $attributes = $client->getUserAttributes();
            // user login or signup comes here

            //Checking facebook email registered yet?
            //Maxsure your registered email when login same with facebook email
            //die(print_r($attributes));

            $email = $attributes['email'];

            $user = Usuario::find()->where(['email'=>$email])->one();
            if(!empty($user)){
                return Yii::$app->user->login($user);
            }else{
                //die(print_r($attributes));
                // Save session attribute user from FB

                $session = Yii::$app->session;
                $session['attributes']=$attributes;

                // redirect to form signup, variabel global set to successUrl
                $this->successUrl = \yii\helpers\Url::to(['signup']);

                $usuario = new Usuario();
                $usuario->tipo = 2;
                $usuario->email = $email;
                $usuario->password = "passtemporal";
                $usuario->auth_token = Security::createToken();
                $usuario->nombre = "name";
                $usuario->appat = "appat";
                $usuario->apmat = "a";

                if ($usuario->save()) {
                    $user = Usuario::find()->where(['email'=>$email])->one();
                    return Yii::$app->user->login($user);
                }

            }
    }

    public function actionLinkedin($client)
    {
        $attributes = $client->getUserAttributes();
            // user login or signup comes here

            //Checking facebook email registered yet?
            //Maxsure your registered email when login same with facebook email
            die(print_r($attributes));
    }

    public function actionIndex()
    {
        if (Yii::$app->user->identity) {
            return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
        }

        //return $this->render('index');
        //////return $this->redirect(array('login'));
        $this->layout = "landing1";
        
        return $this->render('//landing/index', [
            'mainindex' => 1,
        ]);
    }
    public function actionResetpassword()
    {
        $model = new RecuperarPassForm();
        return $this->render('resetpassword', [
            'model' => $model,
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
          if (Yii::$app->user->identity->tipo == Usuario::$EMPRENDEDOR){
            Yii::$app->session->setFlash('welcome', 'Te invitamos a que completes los datos de tu perfil,  ya que son necesarios para que puedas crear tus proyectos.');
            if (!Badge::checkBadgeSoyYo(Yii::$app->user->identity)) {
                Yii::$app->session->setFlash('alert', 'Te invitamos a que completes los datos de tu perfil,  ya que son necesarios para que puedas crear tus proyectos.');
                $catalogos = Usuario::getCatalogos();
                return $this->render('//templates/perfil-usuario-tpl', ['catalogos' => $catalogos]);
            }
            //return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
          }
          // Registrar el evento en el Log
            LogEvento::register(Yii::$app->user->id, LogEvento::$INICIAR_SESSION);
            
            return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
            
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    public function actionLoginajax()
    {
        $email = Yii::$app->request->post('email');
        $pass = Yii::$app->request->post('pass');   
        
        $model = new LoginForm();
        $model->username = $email;
        $model->password = $pass;
        
        if ($model->login())
        {
            return 1;
        }
            
        return 0;
    }
    public function actionRedirectlogin()
    {
      if (Yii::$app->user->identity->tipo == Usuario::$EMPRENDEDOR){
        if (!Badge::checkBadgeSoyYo(Yii::$app->user->identity)) {
            Yii::$app->session->setFlash('alert', 'Te invitamos a que completes los datos de tu perfil,  ya que son necesarios para que puedas crear tus proyectos.');
            $catalogos = Usuario::getCatalogos();
            return $this->render('//templates/perfil-usuario-tpl', ['catalogos' => $catalogos]);
        }
        //return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
      }
      // Registrar el evento en el Log
        LogEvento::register(Yii::$app->user->id, LogEvento::$INICIAR_SESSION);
        
        return $this->redirect([Usuario::$HOME[Yii::$app->user->identity->tipo]]);
    }

    public function actionLogout()
    {
        // Registrar el evento en el Log
        LogEvento::register(Yii::$app->user->id, LogEvento::$CERRAR_SESSION);

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionLanding()
    {
        $this->layout = "landing1";
        return $this->render('landing');
    }
    
    public function actionNoticias(){
        $this->layout = "landing1";
        return $this->render('//landing/noticias');
    }
    
    public function actionNoticia(){
        
    }

    public function actionGetultimosconcursos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $concursos = Concurso::getAll(1, 6);
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
                                'app\models\Institucion' => [
                                    'nombre',
                                ]
                            ]);

        return [
            'total' => count($arrConcursos),
            'result' => $arrConcursos
        ];
    }

    public function actionPoliticas()
    {
        return $this->render('politicas');
    }

    public function actionTooglewidgetsidebar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        Yii::$app->session->set(Yii::$app->request->get('widget_sidebar'), Yii::$app->request->get('active'));

        return [Yii::$app->request->get('widget_sidebar'), Yii::$app->session->get(Yii::$app->request->get('widget_sidebar'))];
    }
}
