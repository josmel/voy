<?php

class Admin_Model_Role extends Core_Model {

    protected $_tableRole;

    public function __construct() {
        $this->_tableRole = new Application_Model_DbTable_Role();
    }

    public function getRoleAll() {
        $smt = $this->_tableRole->getAdapter()->select()
                        ->from($this->_tableRole->getName())
                        ->where("state LIKE '1'")->query();

        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['idrol']] = $row['name'];
        }
        $smt->closeCursor();
        return $result;
    }

    public function getRoleId($id) {
        $smt = $this->_tableRole->getAdapter()->select()->distinct()
                ->from($this->_tableRole->getName())
                ->where("idrol = ?", $id)
                ->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }

//    public function getRoleAlls() {
//        $smt = $this->_tableRole->getAdapter()->select()
//                        ->from($this->_tableRole->getName())
//                        ->where("state LIKE '1'")
//                        ->order('idrol DESC')->query();
//        $result = $smt->fetchAll();
//        $smt->closeCursor();
//        return $result;
//    }

}
