<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "empleados".
 *
 * @property int $IdEmpleado
 * @property string|null $Apellido
 * @property string|null $Nombre
 * @property int|null $NroDoc
 * @property string|null $Domicilio
 * @property int|null $IdTipoEmpleado
 * @property string|null $CUIL
 * @property string|null $Telefono
 * @property int|null $Legajo
 * @property int|null $IdLocalidad
 * @property int|null $IdCondicion
 * @property string|null $FechaIngreso
 * @property int|null $IdCargo
 * @property int|null $IdEmpresa
 * @property int|null $IdJornada
 * @property float|null $Contribucion
 * @property float|null $Aportes
 */
class Empleados extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empleados';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdEmpleado'], 'required'],
            [['IdEmpleado', 'NroDoc', 'IdTipoEmpleado', 'Legajo', 'IdLocalidad', 'IdCondicion', 'IdCargo', 'IdEmpresa', 'IdJornada'], 'integer'],
            [['FechaIngreso'], 'safe'],
            [['Contribucion', 'Aportes'], 'number'],
            [['Apellido', 'Nombre', 'Domicilio'], 'string', 'max' => 50],
            [['CUIL'], 'string', 'max' => 15],
            [['Telefono'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdEmpleado' => 'Id',
            'Apellido' => 'Apellido',
            'Nombre' => 'Nombre',
            'NroDoc' => 'Nro Doc',
            'Domicilio' => 'Domicilio',
            'IdTipoEmpleado' => 'Tipo Empleado',
            'CUIL' => 'Cuil',
            'Telefono' => 'Telefono',
            'Legajo' => 'Legajo',
            'IdLocalidad' => 'Localidad',
            'IdCondicion' => 'Condicion',
            'FechaIngreso' => 'Fecha Ingreso',
            'IdCargo' => 'Cargo',
            'IdEmpresa' => 'Empresa',
            'IdJornada' => 'Jornada',
            'Contribucion' => 'Contribucion',
            'Aportes' => 'Aportes',
        ];
    }
}
