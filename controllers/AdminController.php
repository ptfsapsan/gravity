<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\User;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;

class AdminController extends Controller
{
    public $layout = 'admin';
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isAdmin(Yii::$app->user->identity->username);
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->get();
        if (!empty($get['act']) && $get['act'] == 'delete') {
            User::delete($get['id']);
            return $this->redirect('admin');
        }
        $post = Yii::$app->request->post();
        if (!empty($post['act']) && $post['act'] == 'add') {
            User::add($post['LoginForm']);
            return $this->redirect('admin');
        }

        $model = new LoginForm();

        return $this->render('index', ['users' => User::getAllUsers(), 'model' => $model]);
    }

}
