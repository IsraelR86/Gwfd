<?php

namespace app\controllers;

use Yii;
use app\models\Etiqueta;
use app\models\EtiquetasXNoticia;
use app\models\Noticia;
use app\models\NoticiaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * NoticiaController implements the CRUD actions for Noticia model.
 */
class NoticiaController extends Controller
{
    // Catálogos utilizados al crear y actualizar
    private $catalogs = null;

    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Noticia';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Noticias';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if ( Yii::$app->user->identity->isAdministrador() ) {
                                return true;
                            }

                            throw new \yii\web\ForbiddenHttpException('No tiene permitido el acceso a esta sección.');
                        }
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
    * This method is invoked right before an action is to be executed (after all possible filters.)
    */
    public function beforeAction($action)
    {
        $this->catalogs = [];

        return parent::beforeAction($action);
    }

    /**
     * Lists all Noticia models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NoticiaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Displays a single Noticia model.
     * @param string $id
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
     * Creates a new Noticia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $etiquetas = ArrayHelper::map(Etiqueta::find()->orderBy('descripcion')->all(), 'id', 'descripcion');
        $model = new Noticia();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->portada = UploadedFile::getInstance($model, 'portada');
            if ($model->portada) {
                $model->subirPortada();
            }

            foreach (Yii::$app->request->post('Noticia')['etiquetas'] as $id_etiqueta) {
                $etiqueta = new EtiquetasXNoticia();
                $etiqueta->id_noticia = $model->id;
                $etiqueta->id_etiqueta = $id_etiqueta;
                $etiqueta->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'etiquetas' => $etiquetas,
            ]);
        }
    }

    /**
     * Updates an existing Noticia model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $etiquetas = ArrayHelper::map(Etiqueta::find()->orderBy('descripcion')->all(), 'id', 'descripcion');
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->portada = UploadedFile::getInstance($model, 'portada');
            if ($model->portada) {
                $model->subirPortada();
            }

            EtiquetasXNoticia::deleteAll(['id_noticia' => $model->id]);

            foreach (Yii::$app->request->post('Noticia')['etiquetas'] as $id_etiqueta) {
                $etiqueta = new EtiquetasXNoticia();
                $etiqueta->id_noticia = $model->id;
                $etiqueta->id_etiqueta = $id_etiqueta;
                $etiqueta->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'etiquetas' => $etiquetas,
            ]);
        }
    }

    /**
     * Deletes an existing Noticia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Noticia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Noticia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Noticia::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('La página solicitada no existe.');
        }
    }
}
