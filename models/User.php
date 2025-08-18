<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $apellido
 * @property string $nombre
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $id_perfil
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() { 
        return [ 
        [['username', 'apellido', 'nombre', 'password_hash', 'email'], 'required'], 
        [['id_perfil'], 'integer'], 
        [['username', 'password_hash', ], 'string', 'max' => 255], 
        [['apellido', 'nombre'], 'string', 'max' => 145], 
        [['username'], 'unique'], 
        [['email'], 'unique'] ]; 
    }

    public function attributeLabels() { 
        return [ 'id' => 'ID', 
        'username' => 'Usuario', 
        'apellido' => 'Apellido', 
        'nombre' => 'Nombre', 
        'auth_key' => 'Auth Key', 
        'password_hash' => 'Contraseña', 
        'password_reset_token' => 'Password Reset Token', 
        'email' => 'Email', 
        'status' => 'Estado', 
        'created_at' => 'Created At', 
        'updated_at' => 'Updated At', 
        'verification_token' => 'Verification Token', 
        'id_perfil' => 'Perfil', ]; 
    }

    public function beforeSave($insert)
	{
		if (!parent::beforeSave($insert)) {
			return false;
		}

		if (!empty($this->password_hash)) {
			$this->password_hash = static::setPassword($this->password_hash);
		}
		return true;
	}

    public function getNombrePerfil($id_perfil)
    {
        return UserPerfil::findOne(['id' => $id_perfil])->perfil;
    }

    public function getUserPerfil()
    {
        return $this->hasOne(UserPerfil::className(), ['id' => 'id_perfil']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        //$this->password_hash = Yii::$app->security->generatePasswordHash($password);
        return Yii::$app->security->generatePasswordHash($password);
    }
    
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

   
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function getMenu($id)
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand(
            'select pa.padre, mi.opcion, mi.url, mi.id_padre, mi.id, mi.icono icono_hijo, pa.icono icono_padre
            from user u 
            join menu_perfil mp on mp.id_perfil = u.id_perfil
            join menu_item mi on mi.id = mp.id_menu
            join menu_padre pa on pa.id = mi.id_padre
            where u.id = '.$id. ' group by pa.padre, mi.opcion, mi.url, mi.id_padre, mi.id, mi.icono, pa.icono
            order by pa.padre, mi.opcion;' );

        $result = $command->queryAll();

        return $result;
    }
}
