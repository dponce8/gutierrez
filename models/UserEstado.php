<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_estado".
 *
 * @property int $id
 * @property string|null $estado
 */
class UserEstado extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_estado';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['estado'], 'string', 'max' => 45],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'estado' => 'Estado',
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserEstadoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserEstadoQuery(get_called_class());
    }
}
