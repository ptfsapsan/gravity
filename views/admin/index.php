<?php
/* @var $this yii\web\View */

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html; ?>
    <link rel="stylesheet" href="/css/admin/index.css">

<h2>Добавить пользователя</h2>
<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<input type="hidden" name="act" value="add">

<?=$form->field($model, 'username')->textInput(['autofocus' => true])?>

<?=$form->field($model, 'password')->passwordInput()?>
<?=$form->field($model, 'role')->radioList(User::getRoles())?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?=Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'login-button'])?>
        </div>
    </div>
    <h2>Список пользователей</h2>

<?php ActiveForm::end();

    if (count($users)) { ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Имя пользователя</th>
            <th>Роль</th>
            <th></th>
        </tr>
        <?php foreach ($users as $user) { ?>
            <tr>
                <td><?=$user['id']?></td>
                <td><?=$user['username']?></td>
                <td>
                    <?=Html::radioList(
                        'role[' . $user['id'] . ']',
                        $user['role'],
                        User::getRoles(),
                        ['data-id' => $user['id'], 'class' => 'user-roles']
                    )?>
                </td>
                <td><a href="/admin?act=delete&id=<?=$user['id']?>">Удалить</a></td>
            </tr>
        <?php } ?>
    </table>

    <script type="text/javascript">
        const elements = document.getElementsByClassName('user-roles');
        for (let i in elements) {
            elements[i].addEventListener('click', function (e) {
                location.href = '/admin?act=role&id=' + this.getAttribute('data-id');
            }, false);
        }
    </script>
<?php }