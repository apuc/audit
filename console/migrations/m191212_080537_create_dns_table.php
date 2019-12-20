<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dns}}`.
 */
class m191212_080537_create_dns_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%dns}}', [
            'id' => $this->primaryKey(),
            'class' => $this->string(10),
            'ttl' =>$this->integer(),
            'type'=> $this->string(10),
            'target'=> $this->string(255),
            'ip' => $this->string(),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dns}}');
    }
}
