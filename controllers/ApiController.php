<?php

namespace app\controllers;

use app\models\User;
use app\services\Jwt;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\base\Exception;
use yii\rest\Controller;
use yii\web\Response;

class ApiController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login',
            ],
        ];

        return $behaviors;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionLogin()
    {
        $get = Yii::$app->request->get();
        $user = User::findByUsername($get['username'] ?? '');
        if (empty($user)) {
            return $this->asJson(['error' => 'no such user']);
        }
        if (!Yii::$app->getSecurity()->validatePassword($get['password'] ?? '', $user->password)) {
            return $this->asJson(['error' => 'wrong password']);
        }

        $jwt = new Jwt();
        return $this->asJson([
            'token' => (string)$jwt->getToken($user->username ?? ''),
        ]);
    }

    public function actionLogout()
    {

    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionData()
    {
        $header = Yii::$app->request->headers;
        $bearer = current($header->toArray()['authorization']);
        $token = substr($bearer, 7);
        $jwt = new Jwt();
        $userName = $jwt->verifyToken($token);
        if ($userName === null) {
            return $this->asJson(['error' => 'wrong userId']);
        }

        $user = (array) User::findByUsername($userName);
        unset($user['password']);
        return $this->asJson(['data' => $user]);
    }

}
