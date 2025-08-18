<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "empleadostipo".
 *
 * @property int $IdTipoEmpleado
 * @property string|null $TipoEmpleado
 */
class Empleadostipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empleadostipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdTipoEmpleado'], 'required'],
            [['IdTipoEmpleado'], 'integer'],
            [['TipoEmpleado'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdTipoEmpleado' => 'Id Tipo Empleado',
            'TipoEmpleado' => 'Tipo Empleado',
        ];
    }
}
