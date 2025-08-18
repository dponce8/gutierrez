<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[UserPerfil]].
 *
 * @see UserPerfil
 */
class UserPerfilQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserPerfil[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserPerfil|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
