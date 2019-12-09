<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%theme}}`.
 */
class m191209_073429_create_theme_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%theme}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%theme}}');
    }
}
