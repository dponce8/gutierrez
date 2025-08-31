<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "concepto".
 *
 * @property int $id
 * @property string|null $concepto
 * @property int|null $id_tipo
 * @property int|null $sistema
 *
 * @property Movimiento[] $movimientos
 * @property ConceptoTipo $tipo
 */
class Concepto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concepto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo', 'sistema'], 'integer'],
            [['concepto'], 'string', 'max' => 245],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoTipo::class, 'targetAttribute' => ['id_tipo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'concepto' => 'Concepto',
            'id_tipo' => 'Tipo',
            'sistema' => 'Sistema',
        ];
    }

    /**
     * Gets query for [[Movimientos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMovimientos()
    {
        return $this->hasMany(Movimiento::class, ['id_concepto' => 'id']);
    }

    /**
     * Gets query for [[Tipo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(ConceptoTipo::class, ['id' => 'id_tipo']);
    }
}
