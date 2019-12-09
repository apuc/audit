<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%site}}`.
 */
class m191126_125657_add_states_column_to_site_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%site}}', 'states', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%site}}', 'states');
    }
}
