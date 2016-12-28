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
            'hosts' => array(
                array("127.0.0.1", "6379")
            ),
            'auth' => false,
            'sid' => $_SERVER["DOCUMENT_ROOT"]."#01"
        ),

    ),
);
?>