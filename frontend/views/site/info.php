<?php
use yii\helpers\Html;

$this->title = 'Инструкция';
?>

<div>
    Чтобы провести мониторинг сайтов перейдите в левом меню на страницу "Проверить Url-ы". <br>
    Вставте в текстовое поле список url. В качестве разделителя используйте перенос строки или запятую. <br>
    Нажмите кнопку "Отправить" и дождитесь результатов выполнения скрипта. <br>
    По завершении монитроринга вас перенаправит на страницу "Аудит". Там вы сможете посмотреть данные
    проведенного мониторинга. <br>
    Самый последний монторинг отображается первым в таблице. <br>
    Если нажать на значок <?= Html::img('/uploads/eye.png', ['width' => '20',]) ?> в правом крайнем столбце таблицы, откроется подробный просмотр данных мониторинга каждого сайта. <br>
    Можно осуществлять поиск аудитов по url. Для этого надо ввести url в соответсвующее поле поиска и нажать Enter.
    Либо нажав на сам url.<br>
    <?= Html::img('/uploads/search.png', ['width' => '200',]) ?> <br>
    Сособом, как показано на картинке можно фильтровать любое поле. <br>
    Можно посмотреть данные о доменах (сайтах), url, dns и внешних ссылках по отдельности.
    Для этого нужно перейти на соответствующие страницы в левом меню. <br>
    На странице пользователи можно посмотреть данные о пользователях которые имеют доступ к данному сайту. <br>
</div>
