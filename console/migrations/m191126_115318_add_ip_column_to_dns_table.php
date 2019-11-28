<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%dns}}`.
 */
class m191126_115318_add_ip_column_to_dns_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dns}}', 'ip', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dns}}', 'ip');
    }
}
