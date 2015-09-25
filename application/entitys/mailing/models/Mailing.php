<?php

class Mailing_Model_Mailing extends Core_Model {
    protected $_tableMailing; 
    
    protected $_nameTableBusinessman = 'tmailing_to_empresario'; 
    public function __construct() {
        $this->_tableMailing = new Application_Model_DbTable_Mailing();
    }
    
    public function save($data) {
        $this->_tableMailing->insert($data);
        $idMailing = $this->_tableMailing->getAdapter()->lastInsertId();

        if($data['template'] == 'contactBusinessman'){
            $data = Zend_Json_Decoder::decode($data['data']);
            $this->_tableMailing->getAdapter()->insert(
                    $this->_nameTableBusinessman, 
                    array('idmailing' => $idMailing, 'codempr' => $data['codempr'])
            );
        }
        
    }
}

