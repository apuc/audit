<?php

use yii\db\Migration;

/**
 * Class m191120_095658_audit
 */
class m191120_095660_audit extends Migration
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

        $this->createTable('audit', [
            'id' => $this->primaryKey(),
            'server_response_code' => $this->string(100),
            'size' => $this->integer(),
            'loading_time' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'google_indexing' => $this->boolean(),
            'yandex_indexing' => $this->boolean(),
            'check_search' => $this->boolean(),
            'screenshot' => $this->string(),
            'url_id'=>'integer NOT NULL REFERENCES url(id)'
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191120_095658_audit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191120_095658_audit cannot be reverted.\n";

        return false;
    }
    */
}
