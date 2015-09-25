<?php

class Application_Model_Acl extends Core_Model
{
    protected $_tableAcl; 
    
    public function __construct() {
        $this->_tableAcl = new Application_Model_DbTable_Acl();
    }
    
    public function getListResources() {
        $acls = $this->_tableAcl->getAll('state = 1');

        return array_map(create_function('$arr', 'return $arr["urlacl"];'), $acls);
    }
    
    public function getAclByRole($idRole) {
        $acls = $this->_tableAcl->getByRole($idRole);
  
        return array_map(create_function('$arr', 'return $arr["urlacl"];'), $acls);
    }
    
    public function getFetchPairsAclByRole($idRole) {
        $acls = $this->_tableAcl->getByRole($idRole);
        
        return $this->fetchPairs($acls);
    }
    
    function getAllAcls() {
        return $this->_tableAcl->getAll('state = 1');
    }
    
    function getFetchPairsAllAcls() {
        return $this->fetchPairs($this->getAllAcls());
    }
}

