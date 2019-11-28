<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%external_links}}`.
 */
class m191120_131255_create_external_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%external_links}}', [
            'id' => $this->primaryKey(),
            'acceptor' => $this->string(100),
            'anchor' => $this->string(255),
            'audit_id'=>'integer NOT NULL REFERENCES audit(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%external_links}}');
    }
}
