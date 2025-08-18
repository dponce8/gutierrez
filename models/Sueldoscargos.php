<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sueldoscargos".
 *
 * @property int $idCargo
 * @property string|null $Cargo
 * @property float|null $Basico
 */
class Sueldoscargos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sueldoscargos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCargo'], 'required'],
            [['idCargo'], 'integer'],
            [['Basico'], 'number'],
            [['Cargo'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idCargo' => 'Id Cargo',
            'Cargo' => 'Cargo',
            'Basico' => 'Basico',
        ];
    }
}
