<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%url}}`.
 */
class m191126_085019_add_ip_column_to_url_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%url}}', 'ip', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%url}}', 'ip');
    }
}
