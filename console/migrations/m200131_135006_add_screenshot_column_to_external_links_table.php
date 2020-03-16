<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%external_links}}`.
 */
class m200131_135006_add_screenshot_column_to_external_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%external_links}}', 'screenshot', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%external_links}}', 'screenshot');
    }
}
