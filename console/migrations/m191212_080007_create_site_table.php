<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%site}}`.
 */
class m191212_080007_create_site_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%site}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'creation_date' => $this->integer(),
            'expiration_date' => $this->integer(),
            'registrar' => $this->string(),
            'states' => $this->string(),
            'theme_id'=>'integer REFERENCES theme(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%site}}');
    }
}
