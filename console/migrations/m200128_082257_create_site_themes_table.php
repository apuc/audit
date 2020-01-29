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
            'site_id'=> 'integer NOT NULL REFERENCES site(id)',
            'theme_id'=> 'integer NOT NULL REFERENCES theme(id)'
        ]);
    }
}
