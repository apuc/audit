<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%indexing}}`.
 */
class m200114_123441_add_iks_column_to_indexing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%indexing}}', 'iks', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%indexing}}', 'iks');
    }
}
