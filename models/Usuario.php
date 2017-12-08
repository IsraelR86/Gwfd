<?php

namespace app\models;

use Yii;
use app\helpers\Security;
use app\helpers\Functions;
use yii\db\Query;

/**
 * This is the model class for table "usuarios".
 *
 * @property integer $id
 * @property integer $tipo
 * @property string $email
 * @property string $password
 * @property string $auth_token
 * @property string $nombre
 * @property string $appat
 * @property string $apmat
 * @property integer $activo
 *
 * @property Emprendedor $emprendedor
 * @property Evaluaciones[] $evaluaciones
 * @property GruposEvXEvaluadores[] $gruposEvXEvaluadores
 */
class Usuario extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    // Tipos de usuarios
    public static $ADMINISTRADOR = 1;
    public static $EMPRENDEDOR   = 2;
    public static $EVALUADOR     = 3;
    public static $INSTITUCION   = 4;

    public static $HOME = [
        1 => 'administrador/concursos',
        2 => 'emprendedor/aplica',
        3 => 'evaluador/concursos',
        4 => 'institucion/concursos',
    ];

    // Para el login
    public $authKey;
    public $accessToken;
    public $password_repeat;
    public $captcha_code;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['captcha_code', 'captcha', 'on' => 'register', 'except' => 'signup'],
            [['email', 'password', 'password_repeat', 'auth_token', 'nombre', 'appat', 'apmat'], 'filter', 'filter' => 'strip_tags'],
            [['email', 'auth_token', 'nombre', 'appat', 'apmat'], 'app\validators\DelspacesValidator'],
            [['tipo', 'email', 'password', 'auth_token'], 'required'],
            [['nombre', 'appat', 'apmat'], 'required', 'except' => 'signup'],
            [['id', 'tipo', 'activo'], 'integer'],
            [['email'], 'string', 'max' => 70],
            [['password', 'nombre', 'appat', 'apmat'], 'string', 'max' => 45],
            [['auth_token'], 'string', 'max' => 80],
            [['password', 'password_repeat'], 'required', 'on' => ['register'], 'except' => ['update', 'signup']],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Los campos de la contraseña deben coincidir', 'on'=>'register', 'except' => ['update', 'signup']],
            ['email', 'email'],
            [['email'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo de usuario',
            'email' => 'Correo Electrónico',
            'password' => 'Contraseña',
            'password_repeat' => 'Repetir Contraseña',
            'auth_token' => 'Token',
            'nombre' => 'Nombre',
            'appat' => 'Apellido Paterno',
            'apmat' => 'Apellido Materno',
            'activo' => 'Activo',
            'captcha_code' => 'Código de verificación',
            'nombre_completo' => 'Nombre',
        ];
    }

    /**
     * @inheritdoc
     *
     */
    public function extraFields()
    {
        $extraFields = parent::extraFields();

        // Se agregan estos campos para poder ser exportados por toArray
        $extraFields['byteimagen'] = 'byteimagen';
        $extraFields['etiquetas'] = 'etiquetas';
        $extraFields['evaluador'] = 'evaluador';

        return $extraFields;
    }

    public static function getUploadDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
               Yii::$app->params['upload_dir'].DIRECTORY_SEPARATOR.
               'usuario'.DIRECTORY_SEPARATOR;
    }

    public function getPathImagen()
    {
        return self::getUploadDir().$this->id.'.jpg';
    }

    public function getByteimagen()
    {
        $pathImagen = $this->pathImagen;

        if (!file_exists($pathImagen)) {
            return 'http://simpleicon.com/wp-content/uploads/user1.png';
        }

        $type = pathinfo($pathImagen, PATHINFO_EXTENSION);
        $imagenByte = file_get_contents($pathImagen);
        $base64Imagen = 'data:image/' . $type . ';base64,' . base64_encode($imagenByte);

        return $base64Imagen;
    }

    /**
     * Devuelve la concatenación de nombre + appat + apmat
     *
     * @return string
     */
    public function getNombre_completo()
    {
        return $this->nombre .' '. $this->appat .' '. $this->apmat;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiquetas()
    {
        return $this->hasMany(Etiqueta::className(), ['id' => 'id_etiqueta'])
            ->viaTable('etiquetas_x_evaluadores', ['id_evaluador' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedor()
    {
        return $this->hasOne(Emprendedor::className(), ['id_usuario' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitucion()
    {
        return $this->hasOne(Institucion::className(), ['id_usuario' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluador()
    {
        return $this->hasOne(Evaluador::className(), ['id_usuario' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluaciones()
    {
        return $this->hasMany(Evaluaciones::className(), ['id_evaluador' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposEvXEvaluadores()
    {
        return $this->hasMany(GruposEvXEvaluadores::className(), ['id_evaluador' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposEvaluadoresVisibles()
    {
        return $this->hasMany(GrupoEvaluadores::className(), ['id' => 'id_grupo_evaluadores'])
            ->viaTable('grupos_ev_x_evaluadores', ['id_evaluador' => 'id'])
            ->where('fecha_inicio_proyectos_visibles <= NOW()')
            ->andWhere('fecha_fin_proyectos_visibles >= NOW()');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposEvaluadores()
    {
        return $this->hasMany(GrupoEvaluadores::className(), ['id' => 'id_grupo_evaluadores'])
            ->viaTable('grupos_ev_x_evaluadores', ['id_evaluador' => 'id']);
    }


    /**
     * Actualiza el cambio activo
     */
    public function set_activo()
    {
        Yii::$app->db->createCommand('UPDATE usuarios SET activo = :activo WHERE id = :id', [
            ':activo' => $this->activo,
            ':id' => $this->id
        ])->execute();
    }

    /**
     * Verifica si el usuario es Administrador
     */
    public function isAdministrador()
    {
        return $this->tipo == self::$ADMINISTRADOR;
    }

    /**
     * Verifica si el usuario es Emprendedor
     */
    public function isEmprendedor()
    {
        return $this->tipo == self::$EMPRENDEDOR;
    }

    /**
     * Verifica si el usuario es Evaluador
     */
    public function isEvaluador()
    {
        return $this->tipo == self::$EVALUADOR;
    }

    /**
     * Verifica si el usuario es Institutcion
     */
    public function isInstitucion()
    {
        return $this->tipo == self::$INSTITUCION;
    }

    /**
     * Obtiene todos los evaluadores que no esten registrados en un grupo del concurso especificado
     *
     * @return array app\models\Usuario
     */
    public static function getEvaluadoresSinGrupo($id_concurso)
    {
        $evaluadoresEnGrupoQuery = (new Query())->select('id_evaluador')
            ->from('grupos_ev_x_evaluadores')
            ->innerJoin('grupos_evaluadores', 'grupos_ev_x_evaluadores.id_grupo_evaluadores = grupos_evaluadores.id')
            ->where('grupos_evaluadores.id_concurso = '.$id_concurso);

        return Usuario::find()
            ->where('tipo = '.self::$EVALUADOR)
            ->andWhere(['not in', 'usuarios.id', $evaluadoresEnGrupoQuery])
            ->all();
    }

    /**
     *
     */
    public static function getCatalogos()
    {
        switch(Yii::$app->user->identity->tipo) {
            case Usuario::$ADMINISTRADOR;
                    $extras = [];
                break;
            case Usuario::$EMPRENDEDOR;
                    $extras = Yii::$app->user->identity->emprendedor->getAttributes();
                break;
            case Usuario::$EVALUADOR;
                    $extras = Yii::$app->user->identity->evaluador->getAttributes();
                break;
            case Usuario::$INSTITUCION   = 4;
                    $extras = Yii::$app->user->identity->institucion->getAttributes();
                break;
        }

        $catalogos = [];

        $catalogos['estado'] = Estado::find()
            ->select('id, descripcion')
            ->all();

        $catalogos['universidad'] = Universidad::find()
            ->select('id, nombre')
            ->where('activo = 1')
            ->orderBy('nombre')
            ->all();

        $catalogos['estado_civil'] = Functions::arrayToObject(Yii::$app->params['estado_civil'], 'id', 'descripcion');

        $catalogos['nivel_educativo'] = Functions::arrayToObject(Yii::$app->params['nivel_educativo'], 'id', 'descripcion');

        $catalogos['genero'] = Functions::arrayToObject(Yii::$app->params['genero'], 'id', 'descripcion');

        $catalogos['ciudad_residencia'] = Ciudad::find()
            ->select('id, descripcion')
            ->where('id_estado = :estado', [':estado' => $extras['id_estado']])
            ->orderBy('descripcion')
            ->all();

        if (isset($extras['id_estado_nacimiento'])) {
            $catalogos['ciudad_nacimiento'] = Ciudad::find()
                ->select('id, descripcion')
                ->where('id_estado = :estado', [':estado' => $extras['id_estado_nacimiento']])
                ->orderBy('descripcion')
                ->all();
        } else {
            $catalogos['ciudad_nacimiento'] = [];
        }

        return $catalogos;
    }

    public function getBadgesXUsuario()
    {
        return $this->hasMany(BadgeXUsuario::className(), ['id_usuario' => 'id']);
    }

    /*********************************************************************************
     * Sección destinada para la implementación de Login
     *********************************************************************************/

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $user = static::find()
            ->where('id = :id', [':id' => (int)$id])
            ->andWhere('activo = :activo', [':activo' => 1])
            ->one();

        return $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::find()
            ->where('auth_token = :token', [':token' => $token])
            ->andWhere('activo = :activo', [':activo' => 1])
            ->one();

        return $user;
    }

    /**
     * Finds user by email
     *
     * @param  string      $email
     * @return static|null
     */
    public static function findByEmail($email, $tipo = null)
    {
        $user = static::find()
            ->where('email = :email', [':email' => $email])
            ->andWhere('activo = :activo', [':activo' => 1])
            ->andFilterWhere(['tipo' => $tipo]) // Solo se aplica el filtro cuando $tipo es distinto de null
            ->one();

        return $user;
    }
    public static function findByEmailFb($email)
    {
        $user = static::find()
            ->where('email = :email', [':email' => $email])
            ->andWhere('activo = :activo', [':activo' => 1])
            ->one();

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === Security::encode($password);
    }
}
