<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%settings}}`.
 */
class m200203_113635_add_domain_age_column_to_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%settings}}', 'domain_age', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%settings}}', 'domain_age');
    }
}
