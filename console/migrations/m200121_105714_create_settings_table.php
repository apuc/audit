<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m200121_105714_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'audit_delay' => $this->integer(),
            'indexing_delay' => $this->integer(),
            'available_audit_time' => $this->integer(),
            'available_indexing_time' => $this->integer(),
            'icon' => $this->boolean(),
            'screenshot' => $this->boolean(),
            'chart' => $this->boolean(),
            'domain' => $this->boolean(),
            'redirect' => $this->boolean(),
            'title' => $this->boolean(),
            'theme' => $this->boolean(),
            'comment' => $this->boolean(),
            'server_response_code' => $this->boolean(),
            'size' => $this->boolean(),
            'loading_time' => $this->boolean(),
            'registrar' => $this->boolean(),
            'states' => $this->boolean(),
            'created_at' => $this->boolean(),
            'days_left' => $this->boolean(),
            'google_indexing' => $this->boolean(),
            'yandex_indexing' => $this->boolean(),
            'google_pages' => $this->boolean(),
            'google_date_cache' => $this->boolean(),
            'iks' => $this->boolean(),
            'ip' => $this->boolean(),
            'dns' => $this->boolean(),
            'acceptor' => $this->boolean(),
            'anchor' => $this->boolean(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
