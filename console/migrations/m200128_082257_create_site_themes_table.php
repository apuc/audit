<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%site_themes}}`.
 */
class m200128_082257_create_site_themes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%site_themes}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%site_themes}}');
    }
}
