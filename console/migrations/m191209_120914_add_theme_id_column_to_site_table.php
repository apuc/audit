<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%site}}`.
 */
class m191209_120914_add_theme_id_column_to_site_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%site}}', 'theme_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%site}}', 'theme_id');
    }
}
