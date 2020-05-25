<?php

namespace app\models;

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
}
