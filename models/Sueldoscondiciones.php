<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sueldoscondiciones".
 *
 * @property int $IdCondicion
 * @property string|null $Condicion
 */
class Sueldoscondiciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sueldoscondiciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdCondicion'], 'required'],
            [['IdCondicion'], 'integer'],
            [['Condicion'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdCondicion' => 'Id Condicion',
            'Condicion' => 'Condicion',
        ];
    }
}
