<?php
namespace Classes;

include_once APPLICATION_PATH . '/vendor/SolrPhpClient/Apache/Solr/Service.php';


/**
 *
 * @author webmaster
 *        
 */
class Solr
{

    static $_instance;

    protected $_solr;

    /**
     */
    function __construct()
    {
        $options = new \Zend\Config\Config(include APPLICATION_PATH . '/config/autoload/global.php');
        if (!$options->solr)
            throw new Exception('Configuration Solr not available.'); 
        $this->_solr = new \Apache_Solr_Service($options->solr->host, $options->solr->port, $options->solr->folder);
    }

    static function getInstance()
    {
        if (! isset(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

    function getSolr()
    {
        return $this->_solr;
    }
}

?>