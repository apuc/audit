<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%links}}`.
 */
class m191218_140221_add_link_column_to_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%links}}', 'link', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%links}}', 'link');
    }
}
