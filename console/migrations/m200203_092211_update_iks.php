<?php

use yii\db\Migration;

/**
 * Class m200203_092211_update_iks
 */
class m200203_092211_update_iks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('indexing', ['iks' => 0], ['iks' => null]);
        $this->update('indexing', ['status_iks' => 0], ['status_iks' => null]);
        $this->update('indexing', ['google_indexing' => 0], ['google_indexing' => null]);
        $this->update('indexing', ['status_google' => 0], ['status_google' => null]);
        $this->update('indexing', ['yandex_indexing' => 0], ['yandex_indexing' => null]);
        $this->update('indexing', ['status_yandex' => 0], ['status_yandex' => null]);
        $this->update('indexing', ['google_indexed_pages' => 0], ['google_indexed_pages' => null]);
        $this->update('indexing', ['status_indexing_pages' => 0], ['status_indexing_pages' => null]);
        $this->update('indexing', ['status_date_cache' => 0], ['status_date_cache' => null]);
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
