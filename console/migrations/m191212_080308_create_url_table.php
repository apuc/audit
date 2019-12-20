<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%url}}`.
 */
class m191212_080308_create_url_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%url}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(255)->notNull(),
            'ip' => $this->string(),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%url}}');
    }
}
