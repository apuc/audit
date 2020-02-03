<?php

use yii\db\Migration;

/**
 * Class m200203_092211_update_iks
 */
class m200203_113798_update_iks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('indexing', ['iks' => -1], ['iks' => 0]);
        $this->update('indexing', ['google_indexing' => -1], ['google_indexing' => 0]);
        $this->update('indexing', ['yandex_indexing' => -1], ['yandex_indexing' => 0]);
        $this->update('indexing', ['google_indexed_pages' => -1], ['google_indexed_pages' => 0]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200203_092211_update_iks cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200203_092211_update_iks cannot be reverted.\n";

        return false;
    }
    */
}
