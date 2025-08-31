<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BancoCuenta;

/**
 * BancoCuentaSearch represents the model behind the search form of `app\models\BancoCuenta`.
 */
class BancoCuentaSearch extends BancoCuenta
{
    public $banco;
    public $sucursal;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_banco', 'id_sucursal'], 'integer'],
            [['cuenta', 'cbu', 'alias'], 'safe'],
            [['banco', 'sucursal'], 'safe'],
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
        $query = BancoCuenta::find();

        // add conditions that should always apply here
        $query->joinWith(['banco', 'sucursal']);

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
            'id' => $this->id,
            'id_banco' => $this->id_banco,
            'id_sucursal' => $this->id_sucursal,
        ]);

        $query
        ->andFilterWhere(['like', 'banco.banco', $this->banco])
        ->andFilterWhere(['like', 'sueldosempresas.empresa', $this->sucursal])
        ->andFilterWhere(['like', 'cuenta', $this->cuenta])
        ->andFilterWhere(['like', 'cbu', $this->cbu])
        ->andFilterWhere(['like', 'alias', $this->alias]);

        return $dataProvider;
    }
}
