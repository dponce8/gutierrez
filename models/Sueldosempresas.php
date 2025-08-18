<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sueldosempresas".
 *
 * @property int $idEmpresa
 * @property string|null $Empresa
 * @property string|null $Cuit
 */
class Sueldosempresas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sueldosempresas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idEmpresa'], 'required'],
            [['idEmpresa'], 'integer'],
            [['Empresa'], 'string', 'max' => 50],
            [['Cuit'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idEmpresa' => 'Id Empresa',
            'Empresa' => 'Empresa',
            'Cuit' => 'Cuit',
        ];
    }
}
