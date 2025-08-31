<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "concepto_tipo".
 *
 * @property int $id
 * @property string|null $tipo
 *
 * @property Concepto[] $conceptos
 */
class ConceptoTipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concepto_tipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
        ];
    }

    /**
     * Gets query for [[Conceptos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConceptos()
    {
        return $this->hasMany(Concepto::class, ['id_tipo' => 'id']);
    }
}
