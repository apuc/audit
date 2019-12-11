<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%site}}`.
 */
class m191210_074536_add_comment_column_to_site_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%site}}', 'comment', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%site}}', 'comment');
    }
}
