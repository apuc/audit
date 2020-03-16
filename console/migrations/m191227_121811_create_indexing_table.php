<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%indexing}}`.
 */
class m191227_121811_create_indexing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%indexing}}', [
            'id' => $this->primaryKey(),
            'google_indexing' => $this->boolean(),
            'google_indexed_pages' => $this->integer(),
            'date_cache' => $this->string(),
            'yandex_indexing' => $this->boolean(),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%indexing}}');
    }
}
