<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%audit_pending}}`.
 */
class m200110_113905_create_audit_pending_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%audit_pending}}', [
            'id' => $this->primaryKey(),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%audit_pending}}');
    }
}
