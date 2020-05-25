<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m200525_071429_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(20)->notNull()->unique(),
            'password' => $this->string(100)->notNull(),
            'role' => $this->string(5)->notNull(),
        ]);
        $security = Yii::$app->getSecurity();
//        $this->insert('user', ['username' => 'admin', 'password' => $security->generatePasswordHash('admin'), 'role' => 'admin']);
        $this->insert('user', ['username' => 'user', 'password' => $security->generatePasswordHash('user'), 'role' => 'user']);
        $this->createIndex('idx-user-username', 'user', 'username');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
