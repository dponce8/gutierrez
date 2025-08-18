<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Localidades;

/**
 * LocalidadesSearch represents the model behind the search form of `app\models\Localidades`.
 */
class LocalidadesSearch extends Localidades
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdLocalidad', 'id_provincia'], 'integer'],
            [['Localidad', 'codigo_postal'], 'safe'],
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
        $query = Localidades::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'IdLocalidad' => $this->IdLocalidad,
            'id_provincia' => $this->id_provincia,
        ]);

        $query->andFilterWhere(['like', 'Localidad', $this->Localidad])
            ->andFilterWhere(['like', 'codigo_postal', $this->codigo_postal]);

        return $dataProvider;
    }
}
