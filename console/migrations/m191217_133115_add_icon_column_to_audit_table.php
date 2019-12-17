<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%audit}}`.
 */
class m191217_133115_add_icon_column_to_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%audit}}', 'icon', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%audit}}', 'icon');
    }
}
