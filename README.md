Поддержка кэширования Битрикс через phpredis v 1.5
(https://github.com/phpredis/phpredis)

Использование:

- local/lib/cacheengineredis.php положить в /local/lib вашего сайта
- bitrix/.settings_extra.php соотвественно в /bitrix
- В секции 'host' указать IP и порт вашего сервера redis
- Вместо 'host' можно указать 'socket' -  путь к файлу сокета (при этом 'host' игнорируется)
- В секции 'auth' указать пароль или false, если подключение без пароля
- В секции 'db' указать номер базы или false, если используется база по умолчанию
- В секции 'persistent' указать true, если хотите постоянное соединение к базе или false (по умолчанию)
- В секции 'serializer' указать какой сериализатор использовать. Поддерживается 'php' и 'igbinary'. По умолчанию - 'php' 

Bitrix Cache with phpredis v 1.5
(https://github.com/phpredis/phpredis)

Usage:

- Put the file local/lib/cacheengineredis.php into the /local/lib directory of your website
- Put the file bitrix/.settings_extra.php into the /bitrix directory 
- You can specify IP and port of your redis server in the section 'host'
- Instead of 'host', you can specify path to socket in the section 'socket' (section 'host' will be ignored)
- Specify your password to redis server in the section 'auth' or false if you don't use it.
- Specify database number in the section 'db' or false if you use default database.
- Specify true in the section 'persistent' if you want a persistent connection to database, otherwise - false (default)
- Specify serializer in the section 'serialiazer'. 'php' & 'igbinary' is currently supported. Default is 'php' 