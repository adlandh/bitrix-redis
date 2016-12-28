Поддержка кэширования через phpredis v 0.1
(https://github.com/phpredis/phpredis)

Использование:

- local/lib/cacheengineredis.php положить в /local/lib вашего сайта
- bitrix/.settings_extra.php соотвественно в /bitrix
- В секции 'hosts' указать IP и порт вашего сервера redis (можно несколько)
- В секции 'auth' указать пароль или false, если подключение без пароля

Bitrix Cache with phpredis v 1.1
(https://github.com/phpredis/phpredis)

Usage:

- Put the file local/lib/cacheengineredis.php into the /local/lib directory of your website
- Put the file bitrix/.settings_extra.php into the /bitrix directory 
- You can specify IP and port of your memcached server in the section 'hosts' (you can use several servers)
- Specify your password to redis server in the section 'auth' or false if you don't use it.
