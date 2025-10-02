<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Concepto;

/**
 * ConceptoSearch represents the model behind the search form of `app\models\Concepto`.
 */
class ConceptoSearch extends Concepto
{

    public $tipo;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_tipo', 'sistema'], 'integer'],
            [['concepto'], 'safe'],
            [['tipo'], 'safe'],
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
        $query = Concepto::find();

        // add conditions that should always apply here
        $query->joinWith(['tipo' ]);

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
            'concepto.id' => $this->id,
            'id_tipo' => $this->id_tipo,
            'sistema' => $this->sistema,
        ]);

        $query
            ->andFilterWhere(['like', 'concepto_tipo.tipo', $this->tipo])
            ->andFilterWhere(['like', 'concepto', $this->concepto]);

        return $dataProvider;
    }
}
