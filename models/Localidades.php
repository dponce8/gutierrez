<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "localidades".
 *
 * @property int $IdLocalidad
 * @property string|null $Localidad
 * @property int|null $id_provincia
 * @property string|null $codigo_postal
 */
class Localidades extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'localidades';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_provincia'], 'integer'],
            [['Localidad'], 'string', 'max' => 50],
            [['codigo_postal'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdLocalidad' => 'Id',
            'Localidad' => 'Localidad',
            'id_provincia' => 'Provincia',
            'codigo_postal' => 'C.P.',
        ];
    }
}
