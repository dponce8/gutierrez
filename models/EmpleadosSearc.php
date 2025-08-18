<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Empleados;

/**
 * EmpleadosSearc represents the model behind the search form of `app\models\Empleados`.
 */
class EmpleadosSearc extends Empleados
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdEmpleado', 'NroDoc', 'IdTipoEmpleado', 'Legajo', 'IdLocalidad', 'IdCondicion', 'IdCargo', 'IdEmpresa', 'IdJornada'], 'integer'],
            [['Apellido', 'Nombre', 'Domicilio', 'CUIL', 'Telefono', 'FechaIngreso'], 'safe'],
            [['Contribucion', 'Aportes'], 'number'],
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
        $query = Empleados::find();

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
            'IdEmpleado' => $this->IdEmpleado,
            'NroDoc' => $this->NroDoc,
            'IdTipoEmpleado' => $this->IdTipoEmpleado,
            'Legajo' => $this->Legajo,
            'IdLocalidad' => $this->IdLocalidad,
            'IdCondicion' => $this->IdCondicion,
            'FechaIngreso' => $this->FechaIngreso,
            'IdCargo' => $this->IdCargo,
            'IdEmpresa' => $this->IdEmpresa,
            'IdJornada' => $this->IdJornada,
            'Contribucion' => $this->Contribucion,
            'Aportes' => $this->Aportes,
        ]);

        $query->andFilterWhere(['like', 'Apellido', $this->Apellido])
            ->andFilterWhere(['like', 'Nombre', $this->Nombre])
            ->andFilterWhere(['like', 'Domicilio', $this->Domicilio])
            ->andFilterWhere(['like', 'CUIL', $this->CUIL])
            ->andFilterWhere(['like', 'Telefono', $this->Telefono]);

        return $dataProvider;
    }
}
