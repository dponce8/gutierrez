<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_perfil".
 *
 * @property int $id
 * @property string|null $perfil
 */
class UserPerfil extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_perfil';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perfil'], 'string', 'max' => 145],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'perfil' => 'Perfil',
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserPerfilQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserPerfilQuery(get_called_class());
    }
}
