<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%site}}`.
 */
class m191126_125613_add_registrar_column_to_site_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%site}}', 'registrar', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%site}}', 'registrar');
    }
}
