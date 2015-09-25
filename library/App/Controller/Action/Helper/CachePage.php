<?php
require_once 'Zend/Cache.php';
class App_CachePage{
    static $_instance;
    public $_cache;
    function __construct($config)
    {
        $this->_cache = Zend_Cache::factory('Page', 'File', 
                $config['frontend'], 
                $config['backend']);
    }
    static function getInstance($config)
    {
        if (!isset(self::$_instance)){
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }
    function initCache()
    {
        return $this->_cache->start('xxxx');
    }
}
