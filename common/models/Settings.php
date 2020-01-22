<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property int|null $audit_delay
 * @property int|null $indexing_delay
 * @property int|null $available_audit_time
 * @property int|null $available_indexing_time
 * @property bool|null $icon
 * @property bool|null $screenshot
 * @property bool|null $chart
 * @property bool|null $domain
 * @property bool|null $redirect
 * @property bool|null $title
 * @property bool|null $theme
 * @property bool|null $comment
 * @property bool|null $server_response_code
 * @property bool|null $size
 * @property bool|null $loading_time
 * @property bool|null $registrar
 * @property bool|null $states
 * @property bool|null $created_at
 * @property bool|null $days_left
 * @property bool|null $google_indexing
 * @property bool|null $yandex_indexing
 * @property bool|null $google_pages
 * @property bool|null $google_date_cache
 * @property bool|null $iks
 * @property bool|null $ip
 * @property bool|null $dns
 * @property bool|null $acceptor
 * @property bool|null $anchor
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['audit_delay', 'indexing_delay', 'available_audit_time', 'available_indexing_time'], 'integer'],
            [['icon', 'screenshot', 'chart', 'domain', 'redirect', 'title', 'theme', 'comment', 'server_response_code', 'size', 'loading_time', 'registrar', 'states', 'created_at', 'days_left', 'google_indexing', 'yandex_indexing', 'google_pages', 'google_date_cache', 'iks', 'ip', 'dns', 'acceptor', 'anchor'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'audit_delay' => 'Задержка аудита',
            'indexing_delay' => 'Задержка проверки индексации',
            'available_audit_time' => 'Available Audit Time',
            'available_indexing_time' => 'Available Indexing Time',
            'icon' => 'Иконка',
            'screenshot' => 'Скриншот',
            'chart' => 'График',
            'domain' => 'Домен',
            'redirect' => 'Редирект',
            'title' => 'Тайтл',
            'theme' => 'Тема',
            'comment' => 'Комментарий',
            'server_response_code' => 'Код ответа сервера',
            'size' => 'Размер',
            'loading_time' => 'Время загрузки',
            'registrar' => 'Регистратор',
            'states' => 'Состояния',
            'created_at' => 'Создан',
            'days_left' => 'Дней до окончания регистрации',
            'google_indexing' => 'Индексация Google',
            'yandex_indexing' => 'Индексация Yandex',
            'google_pages' => 'Кол-во проиндексированных страниц',
            'google_date_cache' => 'Дата кэша',
            'iks' => 'Икс',
            'ip' => 'Ip',
            'dns' => 'Dns',
            'acceptor' => 'Акцептор',
            'anchor' => 'Анкор',
        ];
    }
}
