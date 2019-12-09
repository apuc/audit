<?php

use yii\db\Migration;

/**
 * Class m191126_112025_dns
 */
class m191126_112025_dns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        $this->createTable('dns', [
            'id' => $this->primaryKey(),
            'class' => $this->string(10),
            'ttl' =>$this->integer(),
            'type'=> $this->string(10),
            'target'=> $this->string(255),
            'site_id'=> 'integer NOT NULL REFERENCES site(id)'
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191126_112025_dns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191126_112025_dns cannot be reverted.\n";

        return false;
    }
    */
}
