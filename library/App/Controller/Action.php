<?php

class App_Controller_Action extends Zend_Controller_Action
{
    
    protected $_flashMessenger = null;
    
    public function init()
    {       
        parent::init();
    }
        
    public function preDispatch()
    {
        
        parent::preDispatch();
        //exit;
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $authStorage = Zend_Auth::getInstance()->getStorage()->read();
            $isAuth = true;
        } else {
            $authStorage = null;
            $isAuth = false;
        }        
    }

  
    public function getMessenger()
    {
        return $this->_flashMessenger;
    }

    /**
     *
     * @see Zend/Controller/Zend_Controller_Action::getRequest()
     * @return Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        return parent::getRequest();
    }

    /**
     * Retorna un objeto Zend_Config con los parámetros de la aplicación
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        return Zend_Registry::get('config');
    }

    /**
     * Retorna el objeto cache de la aplicación
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return Zend_Registry::get('cache');
    }

    /**
     * Retorna el adaptador
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return Zend_Registry::get('db');
    }

    /**
     * Retorna el objeto Zend_Log de la aplicación
     *
     * @return Zend_Log
     */
    public function getLog()
    {
        return Zend_Registry::get('log');
    }

    
}