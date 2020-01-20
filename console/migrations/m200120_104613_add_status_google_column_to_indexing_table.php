<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%indexing}}`.
 */
class m200120_104613_add_status_google_column_to_indexing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%indexing}}', 'status_google', $this->integer());
        $this->addColumn('{{%indexing}}', 'status_yandex', $this->integer());
        $this->addColumn('{{%indexing}}', 'status_date_cache', $this->integer());
        $this->addColumn('{{%indexing}}', 'status_indexing_pages', $this->integer());
        $this->addColumn('{{%indexing}}', 'status_iks', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%indexing}}', 'status_google');
        $this->dropColumn('{{%indexing}}', 'status_yandex');
        $this->dropColumn('{{%indexing}}', 'status_date_cache');
        $this->dropColumn('{{%indexing}}', 'status_indexing_pages');
        $this->dropColumn('{{%indexing}}', 'status_iks');
    }
}
