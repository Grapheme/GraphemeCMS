Установка и настройка Grapheme CMS

----------------------------------------------------------------------------------------------------
Установка Composer
Laravel использует Composer (http://getcomposer.org/) для управления зависимостями.
Для начала скачайте файл composer.phar. Дальше вы можете либо оставить этот Phar-архив (http://php.net/manual/ru/book.phar.php) в своей локальной папке с проектом,
либо переместить его в /usr/local/bin, чтобы использовать его в рамках всей системы. Для Windows вы можете использовать официальный установщик
(https://getcomposer.org/Composer-Setup.exe).

Run this in your terminal to get the latest Composer version: curl -sS https://getcomposer.org/installer | php
Or if you don't have curl: php -r "readfile('https://getcomposer.org/installer');" | php

Если вы используете Apache в качестве вёб-сервера обязательно включите модуль mod_rewrite.
----------------------------------------------------------------------------------------------------

1) клонируем проект с репозитория - git clone <адрес на гитхабе>
2) Переходим в каталог проекта.
3) устанавливаем зависимости - php composer.phar install
4) создаем базу данных. Имя БД можно можно узнать из файла app/config/database.php [mysql.database];
5) Не обязательно! В корне проекта создаем файл .htaccess (если он не существует). Вносим в него следующий текст:
#---------------------------
AddDefaultCharset utf-8
Options +FollowSymLinks
Options -Indexes

php_value upload_max_filesize 20M
php_value post_max_size 20M
php_value max_execution_time 500
php_value max_input_time 500

<IfModule mod_rewrite.c>
    RewriteEngine on
	RewriteRule (.*) /public/$1 [L]
</IfModule>
#---------------------------
6) Миграции и заполнения таблиц БД: php artisan migrate --seed
7) Логины и пароли можно узнать из файла app/database/seeds/UserTableSeeder.php
    'email'=>'admin@grapheme-cms.ru'
    'password'=>Hash::make('grapheme1234')

    Логин: admin@grapheme-cms.ru
    Пароль: grapheme1234
8) Ввести URL-адрес в браузере

Дополнительно:
Настройка среды окружения для локальной работы
1) Открыть файл bootstrap/start.php
2) Найти запись и добавить информацию о новой среде разработки
Способ 1-й: использовать значения по умолчанию см. файлы из app/config/local/
$env = $app->detectEnvironment(array(
    ....
    'local' => array('ИМЯ КОМПЬЮТЕРА1','ИМЯ КОМПЬЮТЕРА2'),
    ....
));
Способ 2-й: создать независимую среду
$env = $app->detectEnvironment(array(
    ....
    'my_name' => array('ИМЯ МОЕГО КОМПЬЮТЕРА'),
    ....
));
создать каталог my_name, скопировать нужные файлы конфигурации. Можно воспользоваться из каталога app/config/local/ внеся нужные изменения
http://laravel.com/docs/configuration#environment-configuration