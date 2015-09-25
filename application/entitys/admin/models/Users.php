<?php

class Admin_Model_Users extends Core_Model {

    protected $_tableUsers;

    public function __construct() {
        $this->_tableUsers = new Application_Model_DbTable_User();
    }

    public function getUserId($id) {
        $smt = $this->_tableUsers->getAdapter()->select()->distinct()
                        ->from($this->_tableUsers->getName())
                        ->where("iduser = ?", $id)
                        ->where("state LIKE '1'")->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }

    public function getUserRoles($id) {
        $smt = $this->_tableUsers->getAdapter()->select()->distinct()
                        ->from($this->_tableUsers->getName())
                        ->where("idrol = ?", $id)
                        ->where("state LIKE '1'")->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }
    
    public function updateUsersPass($pass, $cod) {
        $data = array(
            'lastpasschange' => date('Y-m-d H:i:s'),
            'password' => $pass
        );
        $this->_tableUsers->update($data, 'iduser = ' . $cod . '');
    }

}
