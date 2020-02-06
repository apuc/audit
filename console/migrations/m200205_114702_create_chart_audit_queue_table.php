<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chart_audit_queue}}`.
 */
class m200205_114702_create_chart_audit_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chart_audit_queue}}', [
            'id' => $this->primaryKey(),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%chart_audit_queue}}');
    }
}
