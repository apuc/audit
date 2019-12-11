<?php

use yii\db\Migration;

/**
 * Class m191120_092739_site
 */
class m191120_092741_site extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('site', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'creation_date' => $this->integer(),
            'expiration_date' => $this->integer(),
            'registrar' => $this->string(),
            'states' => $this->string(),
            'comment' => $this->string(),
            'theme_id'=>'integer REFERENCES theme(id)'
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191120_092739_site cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191120_092739_site cannot be reverted.\n";

        return false;
    }
    */
}
