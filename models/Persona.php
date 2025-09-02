<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "persona".
 *
 * @property int $id
 * @property string|null $apellido
 * @property string|null $nombre
 * @property string|null $cuit
 * @property string|null $domicilio
 * @property int|null $id_localidad
 * @property int|null $id_provincia
 * @property string|null $fijo
 * @property string|null $celular
 * @property string|null $email
 * @property int|null $id_tipo_persona
 */
class Persona extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_localidad', 'id_provincia'], 'required'],
            [['id_localidad', 'id_provincia', 'id_tipo_persona'], 'integer'],
            [['apellido', 'nombre'], 'string', 'max' => 145],
            [['cuit'], 'string', 'max' => 13],
            [['domicilio'], 'string', 'max' => 245],
            [['fijo', 'celular', 'email'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apellido' => 'Apellido',
            'nombre' => 'Nombre',
            'cuit' => 'Cuit',
            'domicilio' => 'Domicilio',
            'id_localidad' => 'Localidad',
            'id_provincia' => 'Provincia',
            'fijo' => 'Fijo',
            'celular' => 'Celular',
            'email' => 'Email',
            'id_tipo_persona' => 'Tipo Persona',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvincia()
    {
        return $this->hasOne(Provincia::class, ['id' => 'id_provincia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonaTipo()
    {
        return $this->hasOne(PersonaTipo::class, ['id' => 'id_tipo_persona']);
    }
}
