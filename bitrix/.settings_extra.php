<?php
/**
 * Created by PhpStorm.
 * User: dh
 * Date: 28.12.16
 * Time: 14:57
 */
return array(
    'cache' => array(
        'value' => array(
            'type' => array(
                'class_name' => '\DHCache\CacheEngineRedis',
                'required_file' => 'lib/cacheengineredis.php'
            ),
            /* you can use only 'host' or only 'socket', not both at the same time */
            'host' => array('127.0.0.1', '6379'),
            'socket' => '/run/redis/redis.sock',
            'auth' => false,
            'db'   => false,
            'sid' => $_SERVER["DOCUMENT_ROOT"].'#01'
        ),

    ),
);
?>