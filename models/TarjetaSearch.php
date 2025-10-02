<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tarjeta;

/**
 * TarjetaSearch represents the model behind the search form of `app\models\Tarjeta`.
 */
class TarjetaSearch extends Tarjeta
{
    public $tipoTarjeta;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tipo'], 'integer'],
            [['tarjeta'], 'safe'],
            [['tipoTarjeta'], 'safe'],
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
        $query = Tarjeta::find();

        // add conditions that should always apply here
        $query->joinWith(['tipoTarjeta' ]);

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
            'tarjeta.id' => $this->id,
            'tipo' => $this->tipo,
        ]);

        $query
        ->andFilterWhere(['like', 'tarjeta_tipo.tipo', $this->tipoTarjeta])
        ->andFilterWhere(['like', 'tarjeta', $this->tarjeta]);

        return $dataProvider;
    }
}
