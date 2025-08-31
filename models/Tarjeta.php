<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tarjeta".
 *
 * @property int $id
 * @property string|null $tarjeta
 * @property int|null $tipo 1= Crédito; 2 = Débito
 */
class Tarjeta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tarjeta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'integer'],
            [['tarjeta'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tarjeta' => 'Tarjeta',
            'tipo' => 'Tipo',
        ];
    }

    public function getTipoTarjeta()
    {
        return $this->hasOne(TarjetaTipo::class, ['id' => 'tipo']);
    }
}
