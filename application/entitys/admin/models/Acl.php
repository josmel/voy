<?php

class Admin_Model_Acl extends Core_Model {

    protected $_tableAcl;

    public function __construct() {
        $this->_tableAcl = new Application_Model_DbTable_Acl();
    }

    public function getGreatAll() {
        $smt = $this->_tableAcl->getAdapter()->select()
                        ->from($this->_tableAcl->getName())
                        ->where("state LIKE '1'")->query();

        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['idacl']] = $row['urlacl'];
        }
        $smt->closeCursor();
        return $result;
    }

    public function getFeaturedId($id) {
        $smt = $this->_tableAcl->getAdapter()->select()->distinct()
                        ->from($this->_tableAcl->getName())
                        ->where("idgreat = ?", $id)->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }



}
