<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%audit}}`.
 */
class m191212_080350_create_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%audit}}', [
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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%audit}}');
    }
}
