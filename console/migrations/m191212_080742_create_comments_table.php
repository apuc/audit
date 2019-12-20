<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comments}}`.
 */
class m191212_080742_create_comments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%comments}}', [
            'id' => $this->primaryKey(),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)',
            'owner_id'=> 'integer NOT NULL REFERENCES user(id)',
            'destination_id'=> 'integer REFERENCES user(id)',
            'comment' => $this->string(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%comments}}');
    }
}
