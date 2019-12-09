<?php

use yii\db\Migration;

/**
 * Class m191120_095657_url
 */
class m191120_095657_url extends Migration
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

        $this->createTable('url', [
            'id' => $this->primaryKey(),
            'url' => $this->string(255)->notNull(),
            'dns' =>$this->string(255),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191120_095657_url cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191120_095657_url cannot be reverted.\n";

        return false;
    }
    */
}
