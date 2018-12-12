<?
/**
 * Created by PhpStorm.
 * User: dh
 * Date: 28.12.16
 * Time: 14:57
 */

namespace DHCache;

use Bitrix\Main\Config\Configuration;

class CacheEngineRedis implements \Bitrix\Main\Data\ICacheEngine, \Bitrix\Main\Data\ICacheEngineStat
{
    /*
     * @var obRedis - connection to redis.
     */
    private static $obRedis = null;

    /*
     * @var isConnected -  is already connected
     */
    private static $isConnected = false;

     /*
     * @var read - bytes read
     */

    private $read = false;

    /*
     * @var written - bytes written
    */

    private $written = false;

    /*
     * @var baseDirVersion - array of base_dir
     */
    private static $baseDirVersion = array();

    /*
     * @var key - stored key
     */

    private $key = '';

    /*
     * Constructor
     */

    function __construct()
    {
        $cacheConfig = Configuration::getValue("cache");

        if (self::$obRedis == null) {
            self::$obRedis = new \Redis();

            if (isset($cacheConfig["socket"])) {
                if ($cacheConfig["persistent"]) {
                    self::$isConnected = self::$obRedis->pconnect($cacheConfig["socket"]);
                } else {
                    self::$isConnected = self::$obRedis->connect($cacheConfig["socket"]);
                }
            } elseif (isset($cacheConfig["host"])) {
                $host = $cacheConfig["host"];

                if (empty($host[0])) {
                    $host[0] = "127.0.0.1";
                }
                if (empty($host[1])) {
                    $host[0] = "6379";
                }
                if ($cacheConfig["persistent"]) {
                    self::$isConnected = self::$obRedis->pconnect($host[0], $host[1]);
                } else {
                    self::$isConnected = self::$obRedis->connect($host[0], $host[1]);
                }


            } else {
                if ($cacheConfig["persistent"]) {
                    self::$isConnected = self::$obRedis->pconnect("127.0.0.1");
                } else {
                    self::$isConnected = self::$obRedis->connect("127.0.0.1");
                }
            }

            if ($cacheConfig["auth"] && self::$isConnected) {
                self::$isConnected = self::$obRedis->auth($cacheConfig["auth"]);
            }

            if ($cacheConfig["db"] && self::$isConnected) {
                self::$isConnected = self::$obRedis->select($cacheConfig["db"]);
            }

        }

        if ($cacheConfig && is_array($cacheConfig)) {
            if (!empty($cacheConfig["sid"])) {
                self::$obRedis->setOption(\Redis::OPT_PREFIX, $cacheConfig["sid"]); ;
            }
        }
        if (self::$isConnected) {
            if (!empty($cacheConfig["serializer"]) && $cacheConfig["serializer"] == 'igbinary') {
                self::$obRedis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
            } else {
                self::$obRedis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            }
        }
    }

    /*
     * Close connection
     *
     * @return void
     */

    public function close()
    {
        if (self::$obRedis != null) {
            self::$obRedis->close();
        }
    }

    /*
     * Returns number of bytes read from redis or false if there were no read operation
     *
     * @return integer|false
     */

    public function getReadBytes()
    {
        return $this->read;
    }

    /*
     * Returns number of bytes written to redis or false if there were no write operation
     *
     * @return integer|false
     */
    public function getWrittenBytes()
    {
        return $this->written;
    }

    /*
     * Always return ""
     *
     * @return ""
     *
     */
    public function getCachePath()
    {
        //return $this->key;
        return "";
    }

    /*
     * Returns true if there's connection to redis
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return self::$isConnected;
    }

    /**
     * Cleans (removes) cache directory or file.
     *
     * @param string $baseDir Base cache directory.
     * @param string $initDir Directory within base.
     * @param string $filename File name.
     *
     * @return void
     */
    public function clean($baseDir, $initDir = false, $filename = false)
    {
        if (self::$isConnected) {
            if (strlen($filename)) {
                if (!isset(self::$baseDirVersion[$baseDir])) {
                    self::$baseDirVersion[$baseDir] = self::$obRedis->get($baseDir);
                }

                if (self::$baseDirVersion[$baseDir] === false || self::$baseDirVersion[$baseDir] === '') {
                    return;
                }

                if ($initDir !== false) {
                    $initDirVersion = self::$obRedis->get(self::$baseDirVersion[$baseDir] . "|" . $initDir);
                    if ($initDirVersion === false || $initDirVersion === '') {
                        return;
                    }
                } else {
                    $initDirVersion = "";
                }

                self::$obRedis->del(self::$baseDirVersion[$baseDir] . "|" . $initDirVersion . "|" . $filename);
            } else {
                if (strlen($initDir)) {
                    if (!isset(self::$baseDirVersion[$baseDir])) {
                        self::$baseDirVersion[$baseDir] = self::$obRedis->get($baseDir);
                    }

                    if (self::$baseDirVersion[$baseDir] === false || self::$baseDirVersion[$baseDir] === '') {
                        return;
                    }


                    self::$obRedis->del(self::$baseDirVersion[$baseDir] . "|" . $initDir);
                } else {
                    if (isset(self::$baseDirVersion[$baseDir])) {
                        unset(self::$baseDirVersion[$baseDir]);
                    }

                    self::$obRedis->del($baseDir);
                }
            }
        }
    }

    /**
     * Reads cache from the redis. Returns true if key value exists, not expired, and successfully read.
     *
     * @param mixed &$arAllVars Cached result.
     * @param string $baseDir Base cache directory.
     * @param string $initDir Directory within base.
     * @param string $filename File name.
     * @param integer $TTL Expiration period in seconds.
     *
     * @return boolean
     */
    public function read(&$arAllVars, $baseDir, $initDir, $filename, $TTL)
    {
        if (self::$isConnected) {
            if (!isset(self::$baseDirVersion[$baseDir])) {
                self::$baseDirVersion[$baseDir] = self::$obRedis->get($baseDir);
            }

            if (self::$baseDirVersion[$baseDir] === false || self::$baseDirVersion[$baseDir] === '') {
                return false;
            }

            if ($initDir !== false) {
                $initDirVersion = self::$obRedis->get(self::$baseDirVersion[$baseDir] . "|" . $initDir);
                if ($initDirVersion === false || $initDirVersion === '') {
                    return false;
                }
            } else {
                $initDirVersion = "";
            }

            $this->key = self::$baseDirVersion[$baseDir] . "|" . $initDirVersion . "|" . $filename;

            $arAllVars = self::$obRedis->get($this->key);

            if ($arAllVars === false || $arAllVars === '') {
                $this->read = 0;
                return false;
            } else {
                $this->read = self::$obRedis->strlen($this->key);
            }

            return true;
        } else {
            return false;
        }

    }

    /**
     * Puts cache into the redis.
     *
     * @param mixed $arAllVars Cached result.
     * @param string $baseDir Base cache directory.
     * @param string $initDir Directory within base.
     * @param string $filename File name.
     * @param integer $TTL Expiration period in seconds.
     *
     * @return void
     */
    public function write($arAllVars, $baseDir, $initDir, $filename, $TTL)
    {
        if (self::$isConnected) {
            if (!isset(self::$baseDirVersion[$baseDir])) {
                self::$baseDirVersion[$baseDir] = self::$obRedis->get($baseDir);
            }

            if (self::$baseDirVersion[$baseDir] === false || self::$baseDirVersion[$baseDir] === '') {
                self::$baseDirVersion[$baseDir] = md5(mt_rand());
                self::$obRedis->set($baseDir, self::$baseDirVersion[$baseDir]);
            }

            if ($initDir !== false) {
                $initDirVersion = self::$obRedis->get(self::$baseDirVersion[$baseDir] . "|" . $initDir);
                if ($initDirVersion === false || $initDirVersion === '') {
                    $initDirVersion = md5(mt_rand());
                    self::$obRedis->set(self::$baseDirVersion[$baseDir] . "|" . $initDir, $initDirVersion);
                }
            } else {
                $initDirVersion = "";
            }

            $this->key = self::$baseDirVersion[$baseDir] . "|" . $initDirVersion . "|" . $filename;

            if (self::$obRedis->set($this->key, $arAllVars, $TTL)) {
                $this->written = self::$obRedis->strlen($this->key);
            } else {
                $this->written = 0;
            }

        }

    }

    /**
     * Returns true if cache has been expired.
     * Stub function always returns true.
     *
     * @param string $path Absolute physical path.
     *
     * @return boolean
     */
    function isCacheExpired($path)
    {
        return false;
    }

}