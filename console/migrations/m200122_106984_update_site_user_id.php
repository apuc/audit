<?php

use yii\db\Migration;

class m200122_106984_update_site_user_id extends Migration
{
    public function safeUp()
    {
        $this->update('site', [
            'user_id' => 2,
        ]);
    }

}