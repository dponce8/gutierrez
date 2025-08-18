<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehiculo".
 *
 * @property int $id
 * @property string|null $patente
 * @property int|null $numero_interno
 * @property string|null $marca
 * @property string|null $modelo
 * @property int|null $fabricacion
 * @property int|null $asientos
 * @property int|null $id_estado
 * @property string|null $fecha_alta
 * @property string|null $fecha_baja
 * @property string|null $obs
 */
class Vehiculo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehiculo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero_interno', 'fabricacion', 'asientos', 'id_estado'], 'integer'],
            [['fecha_alta', 'fecha_baja'], 'safe'],
            [['obs'], 'string'],
            [['patente', 'marca', 'modelo'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'patente' => 'Patente',
            'numero_interno' => 'Numero Interno',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'fabricacion' => 'Año Fabricacion',
            'asientos' => 'Cant. Asientos',
            'id_estado' => 'Estado',
            'fecha_alta' => 'Fecha Alta',
            'fecha_baja' => 'Fecha Baja',
            'obs' => 'Observaciones',
        ];
    }
}
