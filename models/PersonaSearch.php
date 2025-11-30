<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Persona;

/**
 * PersonaSearch represents the model behind the search form of `app\models\Persona`.
 */
class PersonaSearch extends Persona
{
    public $provinciaNombre;
    public $tipoPersona;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_localidad', 'id_provincia', 'id_tipo_persona'], 'integer'],
            [['apellido', 'nombre', 'cuit', 'domicilio', 'fijo', 'celular', 'email', 'provinciaNombre', 'tipoPersona'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Persona::find()->joinWith(['provincia', 'personaTipo']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'apellido' => SORT_ASC,
                    'nombre' => SORT_ASC,
                ],
                'attributes' => [
                    'id',
                    'apellido',
                    'nombre',
                    'cuit',
                    'domicilio',
                    'id_localidad',
                    'id_provincia',
                    'fijo',
                    'celular',
                    'email',
                    'id_tipo_persona',
                    'provinciaNombre' => [
                        'asc' => ['provincia.provincia' => SORT_ASC],
                        'desc' => ['provincia.provincia' => SORT_DESC],
                    ],
                    'tipoPersona' => [
                        'asc' => ['persona_tipo.tipo' => SORT_ASC],
                        'desc' => ['persona_tipo.tipo' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'persona.id' => $this->id,
            'persona.id_localidad' => $this->id_localidad,
            'persona.id_provincia' => $this->id_provincia,
            'persona.id_tipo_persona' => $this->id_tipo_persona,
        ]);

        $query->andFilterWhere(['like', 'persona.apellido', $this->apellido])
            ->andFilterWhere(['like', 'persona.nombre', $this->nombre])
            ->andFilterWhere(['like', 'persona.cuit', $this->cuit])
            ->andFilterWhere(['like', 'persona.domicilio', $this->domicilio])
            ->andFilterWhere(['like', 'persona.fijo', $this->fijo])
            ->andFilterWhere(['like', 'persona.celular', $this->celular])
            ->andFilterWhere(['like', 'persona.email', $this->email])
            ->andFilterWhere(['like', 'provincia.provincia', $this->provinciaNombre])
            ->andFilterWhere(['like', 'persona_tipo.tipo', $this->tipoPersona]);

        return $dataProvider;
    }
}
