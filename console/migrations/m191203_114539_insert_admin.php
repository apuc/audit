<?php

use yii\db\Migration;

/**
 * Class m191203_114538_insert_admin
 */
class m191203_114539_insert_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('user', [
            'username' => 'admin',
            'auth_key' => 'ufbOh9JRmxxb5Zjn4b76pCMMdET34wWH',
            'password_hash' => '$2y$13$2exDfOT0YsrbvZQSq2BA3eLgjt4N8yHhegJQEXyiPmGMDd.kWsPgO',
            'email' => 'admin@admin.com',
            'status' => 10,
            'created_at' => 1574425070,
            'updated_at' => 1574425070,
            'verification_token' => 'DyEEMLtH8qEmPd1nOaj9VQusU_X0lnBR_1574425070'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191203_114538_insert_admin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191203_114538_insert_admin cannot be reverted.\n";

        return false;
    }
    */
}
