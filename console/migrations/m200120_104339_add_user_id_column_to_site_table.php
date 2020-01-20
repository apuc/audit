<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%site}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m200120_104339_add_user_id_column_to_site_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%site}}', 'user_id', 'integer REFERENCES user(id)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
