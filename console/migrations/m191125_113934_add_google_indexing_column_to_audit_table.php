<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%audit}}`.
 */
class m191125_113934_add_google_indexing_column_to_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%audit}}', 'google_indexing', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%audit}}', 'google_indexing');
    }
}
