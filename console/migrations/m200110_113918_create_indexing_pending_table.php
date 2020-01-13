<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%indexing_pending}}`.
 */
class m200110_113918_create_indexing_pending_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%indexing_pending}}', [
            'id' => $this->primaryKey(),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%indexing_pending}}');
    }
}
