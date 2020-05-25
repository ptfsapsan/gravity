<?php

namespace app\models;

use damirka\JWT\UserTrait;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\db\Query;
use yii\web\IdentityInterface;

class User extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $role;
    public $authKey;
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public static function getRoles()
    {
        return [
            self::ROLE_USER => 'Пользователь',
            self::ROLE_ADMIN => 'Администратор',
        ];
    }

    // Override this method
    protected static function getSecretKey()
    {
        return Yii::$app->params['jwtSecretKey'];
    }

    // And this one if you wish
    protected static function getHeaderToken()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public static function findIdentity($id)
    {
        if (!$id) {
            return new static([
                'username' => Yii::$app->params['adminUsername'],
                'password' => Yii::$app->getSecurity()->generatePasswordHash(Yii::$app->params['adminPassword']),
                'role' => 'admin',
            ]);
        }
        $user = (new Query())
            ->from('user')
            ->where(['id' => $id])
            ->one();
        return $user ? new static($user) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return new static();
    }

    /**
     * Finds user by username
     * @param string $username
     * @return static|null
     * @throws Exception
     */
    public static function findByUsername($username)
    {
        if (strcasecmp(Yii::$app->params['adminUsername'], $username) === 0) {
            return new static(
                [
                    'username' => Yii::$app->params['adminUsername'],
                    'password' => Yii::$app->getSecurity()->generatePasswordHash(Yii::$app->params['adminPassword']),
                    'role' => 'admin',
                ]
            );
        }

        $user = (new Query())
            ->from('user')
            ->where(['username' => $username])
            ->one();
        return $user ? new static($user) : null;
    }

    /**
     * @param int $id
     * @throws \yii\db\Exception
     */
    public static function delete(int $id)
    {
        Yii::$app->db->createCommand()->delete('user', "id = $id")->execute();
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public static function add(array $data)
    {
        Yii::$app->db->createCommand()
            ->insert('user', [
                'username' => $data['username'],
                'password' => Yii::$app->getSecurity()->generatePasswordHash($data['password']),
                'role' => $data['role'],
            ])->execute();;
    }

    public static function isAdmin($username)
    {
        return $username == Yii::$app->params['adminUsername'];

    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public static function getAllUsers()
    {
        return (new Query())
            ->from('user')
            ->orderBy('id')
            ->all();
    }
}
