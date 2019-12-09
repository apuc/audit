<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%audit}}`.
 */
class m191205_130122_add_screenshot_column_to_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%audit}}', 'screenshot', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%audit}}', 'screenshot');
    }
}
