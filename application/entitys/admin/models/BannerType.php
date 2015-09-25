<?php

class Admin_Model_BannerType extends Core_Model
{
    protected $_tableBannerType; 
    
    public function __construct() {
        $this->_tableBannerType = new Application_Model_DbTable_BannerType();
    }    
  
    public function findById($id) {
        $smt = $this->_tableBannerType->getAdapter()->select()
                ->from($this->_tableBannerType->getName())
                ->where("vchestado LIKE 'A'")
                ->where('codtbanner LIKE ?', $id)
                ->query();
        
        $result = $smt->fetchAll();
        
        if(!empty($result)) {
            $result = $result[0];
        }
        $smt->closeCursor();
        
        return $result;
    }
    
    public function getPairsAll() {
        $smt = $this->_tableBannerType->getAdapter()->select()
                ->from($this->_tableBannerType->getName())
                ->where("vchestado LIKE 'A'")->query();
        
        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['codtbanner']] = $row['nombre'];
        }
        $smt->closeCursor();
        
        return $result;
    }
}
