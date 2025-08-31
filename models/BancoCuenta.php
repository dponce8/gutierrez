<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banco_cuenta".
 *
 * @property int $id
 * @property string|null $cuenta
 * @property int|null $id_banco
 * @property int|null $id_sucursal
 * @property string|null $cbu
 * @property string|null $alias
 *
 * @property Banco $banco
 * @property Sucursal $sucursal
 */
class BancoCuenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banco_cuenta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_banco', 'id_sucursal'], 'integer'],
            [['cuenta', 'cbu', 'alias'], 'string', 'max' => 45],
            [['id_banco'], 'exist', 'skipOnError' => true, 'targetClass' => Banco::class, 'targetAttribute' => ['id_banco' => 'id']],
            [['id_sucursal'], 'exist', 'skipOnError' => true, 'targetClass' => Sueldosempresas::class, 'targetAttribute' => ['id_sucursal' => 'idEmpresa']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cuenta' => 'Cuenta',
            'id_banco' => 'Banco',
            'id_sucursal' => 'Empresa',
            'cbu' => 'CBU',
            'alias' => 'Alias',
        ];
    }

    /**
     * Gets query for [[Banco]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBanco()
    {
        return $this->hasOne(Banco::class, ['id' => 'id_banco']);
    }

    /**
     * Gets query for [[Sucursal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSucursal()
    {
        return $this->hasOne(Sueldosempresas::class, ['idEmpresa' => 'id_sucursal']);
    }
}
