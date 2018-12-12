##Поддержка кэширования Битрикс через phpredis v 1.6
(https://github.com/phpredis/phpredis)

###Использование:

- local/lib/cacheengineredis.php положить в /local/lib вашего сайта
- bitrix/.settings_extra.php соотвественно в /bitrix
- В разделе 'host' указать IP и порт вашего сервера redis
- Вместо 'host' можно указать 'socket' -  путь к файлу сокета (при этом 'host' игнорируется)
- В разделе 'auth' указать пароль или false, если подключение без пароля
- В разделе 'db' указать номер базы или false, если используется база по умолчанию
- В разделе 'persistent' указать true, если хотите постоянное соединение к базе или false (по умолчанию)
- В разделе 'serializer' указать какой сериализатор использовать. Поддерживается 'php' и 'igbinary'. По умолчанию - 'php'
- В разделе 'sid' можно указать префикс, с которым будут сохраняться все данные кэша. Удобно при использовании несколькими сайтами одной базы в Redis. 

###Как настроить Redis:

- Желательно увеличить значение переменной "tcp_backlog" для ускорения подключения при большом количестве клиентов. 
По умолчанию она равно 511. Возможно придется при этом изменить значение /proc/sys/net/core/somaxconn
- Желательно установить в переменную "timeout" время в секундах, после которого отключатся неактивные клиенты.
- Желательно закомментировать все строки с "save" для того, чтоб Redis не сохранял периодически ваш кэш на диск.
- Желательно установить значение в переменную "maxmemory". В противном случае Redis сожрет всю доступную память на сервере.
- Обязательно переменную "maxmemory-policy" устанавливаем в "allkeys-lru". В этом случае при недостатке памяти Redis будет удалять из базы все старые неиспользуемые ключи.

##Bitrix Cache with phpredis v 1.5
(https://github.com/phpredis/phpredis)

##Usage:

- Put the file local/lib/cacheengineredis.php into the /local/lib directory of your website
- Put the file bitrix/.settings_extra.php into the /bitrix directory 
- You can specify IP and port of your redis server in the section 'host'
- Instead of 'host', you can specify path to socket in the section 'socket' (section 'host' will be ignored)
- Specify your password to redis server in the section 'auth' or false if you don't use it.
- Specify database number in the section 'db' or false if you use default database.
- Specify true in the section 'persistent' if you want a persistent connection to database, otherwise - false (default)
- Specify serializer in the section 'serialiazer'. 'php' & 'igbinary' is currently supported. Default is 'php' 
- You can specify the prefix for all the keys of the cache in the section 'sid'. It's convenient when several web sites use one database in Redis

##How to setup Redis:

- It is desirable to increase the value of "tcp_backlog" to decrease connection time with a large number of clients. It's 511 by default.
Maybe it will be necessary to change the value in /proc/sys/net/core/somaxconn
- It is desirable to set the value of "timeout" in seconds to close the connection after a client is idle
- It is desirable to comment out all lines starting from "save" to prevent Redis to save your cache on disk.
- It is desirable to set the value of "maxmemory". Otherwise Redis will use all available memory.
- Be sure to set "maxmemory-policy" to "allkeys-lru". In this case all the old unused keys will be removed when Redis is out of memory.