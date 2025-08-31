<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "persona_movimiento_tipo".
 *
 * @property int $id
 * @property string|null $movimiento
 * @property int|null $debe
 */
class PersonaMovimientoTipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persona_movimiento_tipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['debe'], 'integer'],
            [['movimiento'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'movimiento' => 'Movimiento',
            'debe' => 'Debe',
        ];
    }
}
