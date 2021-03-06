<?php

use yii\db\Migration;

class m200206_106589_insert_settings extends Migration
{
    public function safeUp()
    {
        $date = new DateTime();
        $date = $date->getTimestamp();

        $this->insert('settings', [
            'audit_delay' => 1,
            'indexing_delay' => 3,
            'chart_audit_delay' => 1440,
            'available_audit_time' => $date,
            'available_indexing_time' => $date,
            'chart_audit_time_available' => $date,
            'available_audit_time_all' => $date,
            'sizer' => 500,
            'icon' => true,
            'screenshot' => true,
            'chart' => true,
            'domain' => true,
            'redirect' => true,
            'title' => true,
            'theme' => true,
            'comment' => true,
            'server_response_code' => true,
            'size' => true,
            'loading_time' => true,
            'registrar' => true,
            'states' => true,
            'created_at' => true,
            'domain_age' => true,
            'days_left' => true,
            'google_indexing' => true,
            'yandex_indexing' => true,
            'google_pages' => true,
            'google_date_cache' => true,
            'iks' => true,
            'ip' => true,
            'dns' => true,
            'acceptor' => true,
            'anchor' => true,
        ]);
    }
}