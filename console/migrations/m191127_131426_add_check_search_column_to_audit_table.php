<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%audit}}`.
 */
class m191127_131426_add_check_search_column_to_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%audit}}', 'check_search', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%audit}}', 'check_search');
    }
}
