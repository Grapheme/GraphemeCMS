# Setup and install GRPHM CMS

## 1. Установка Composer
Для работы фреймворка Laravel необходимо уставноить Composer (http://getcomposer.org/). Для этого откройте командную строку и выполните следующую команду: 

`curl -sS https://getcomposer.org/installer | php`

Если у вас не установлен curl, тогда выполните следующую команду:

`php -r "readfile('https://getcomposer.org/installer');" | php`


## 2. Настройка веб-сервера
В настройках вашего веб-сервера обязательно включите модуль переадресации. Например, для веб-сервера Apache необходимо включить модуль mod_rewrite. Для этого необходимо раскоментировать модуль в httpd.conf или на linux серверах выполнить команду:

`a2enmode mod_rewrite`


## 3. Установка и настройка проекта

1. Клонируем проект с репозитория github 

`git clone <адрес на гитхабе>`

1. Переходим в каталог проекта

`cd path/to/folder`

1. Устанавливаем необходимые зависимости - 

`php composer.phar install`

1. Создаем базу данных. Имя БД можно можно узнать из файла app/config/database.php [mysql.database];
1. Не обязательно! В корне проекта создаем файл .htaccess, если он не существует. Вносим в него следующий текст:

`AddDefaultCharset utf-8`

`Options +FollowSymLinks`

`Options -Indexes`

``

`php_value upload_max_filesize 20M`

`php_value post_max_size 20M`

`php_value max_execution_time 500`

`php_value max_input_time 500`

``

`<IfModule mod_rewrite.c>`

`    RewriteEngine on`

`	RewriteRule (.*) /public/$1 [L]`

`</IfModule>`

1. Заполняем базу данных информацией и создаем нужную структуру таблиц: 

`php artisan migrate --seed`

1. Логин и пароль для входа в панель управления можно узнать из файла `app/database/seeds/UserTableSeeder.php`

`'email'=>'admin@grapheme-cms.ru'`

`'password'=>Hash::make('grapheme1234')`

1. Открыть проект в браузере по пути, в который вы установили проект.
Доступ к панели управления осуществялется по ссылке `http://path/login`

## Дополнительно
Настройка среды окружения для локальной работы
1. Открыть файл bootstrap/start.php
2. Найти запись и добавить информацию о новой среде разработки
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