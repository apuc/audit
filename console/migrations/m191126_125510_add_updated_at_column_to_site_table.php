<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%site}}`.
 */
class m191126_125510_add_updated_at_column_to_site_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%site}}', 'expiration_date', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%site}}', 'updated_at');
    }
}
