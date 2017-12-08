<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Emprendedor;

/**
 * EmprendedorSearch represents the model behind the search form about `app\models\Emprendedor`.
 */
class EmprendedorSearch extends Emprendedor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_usuario', 'genero', 'id_nivel_educativo', 'id_estado', 'id_ciudad', 'estado_civil', 'id_estado_nacimiento', 'id_ciudad_nacimiento', 'id_universidad'], 'integer'],
            [['fecha_nacimiento', 'universidad_otro', 'profesion', 'curp', 'rfc', 'tel_celular', 'tel_fijo', 'cp', 'direccion', 'colonia', 'facebook', 'twitter', 'pagina_web'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Emprendedor::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_usuario' => $this->id_usuario,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'genero' => $this->genero,
            'id_nivel_educativo' => $this->id_nivel_educativo,
            'id_estado' => $this->id_estado,
            'id_ciudad' => $this->id_ciudad,
            'estado_civil' => $this->estado_civil,
            'id_estado_nacimiento' => $this->id_estado_nacimiento,
            'id_ciudad_nacimiento' => $this->id_ciudad_nacimiento,
            'id_universidad' => $this->id_universidad,
        ]);

        $query->andFilterWhere(['like', 'universidad_otro', $this->universidad_otro])
            ->andFilterWhere(['like', 'profesion', $this->profesion])
            ->andFilterWhere(['like', 'curp', $this->curp])
            ->andFilterWhere(['like', 'rfc', $this->rfc])
            ->andFilterWhere(['like', 'tel_celular', $this->tel_celular])
            ->andFilterWhere(['like', 'tel_fijo', $this->tel_fijo])
            ->andFilterWhere(['like', 'cp', $this->cp])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'colonia', $this->colonia])
            ->andFilterWhere(['like', 'facebook', $this->facebook])
            ->andFilterWhere(['like', 'twitter', $this->twitter])
            ->andFilterWhere(['like', 'pagina_web', $this->pagina_web]);

        return $dataProvider;
    }
}
