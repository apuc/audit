<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%settings}}`.
 */
class m200205_115047_add_chart_audit_delay_column_to_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%settings}}', 'chart_audit_delay', $this->integer());
        $this->addColumn('{{%settings}}', 'chart_audit_time_available', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%settings}}', 'chart_audit_delay');
        $this->dropColumn('{{%settings}}', 'chart_audit_time_available');
    }
}
