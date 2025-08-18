<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[UserEstado]].
 *
 * @see UserEstado
 */
class UserEstadoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserEstado[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserEstado|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
