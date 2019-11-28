<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%audit}}`.
 */
class m191125_114113_add_yandex_indexing_column_to_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%audit}}', 'yandex_indexing', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%audit}}', 'yandex_indexing');
    }
}
