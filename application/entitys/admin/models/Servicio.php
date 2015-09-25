<?php

class Admin_Model_Servicio extends Core_Model {

    protected $_tableServicio;

    public function __construct() {
        $this->_tableServicio = new Application_Model_DbTable_Servicio();
    }

    public function idImg($id) {
        $smt = $this->_tableServicio->getAdapter()->select()
                        ->from($this->_tableServicio->getName(), array('img'))
                        ->where("idservicio = ?", $id)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }


     public function ServicioAll() {
        $select = $this->_tableServicio->getAdapter()->select()
                        ->from($this->_tableServicio->getName(), array('descripcion','alt', 'url', 'img'))
                        ->where('estado LIKE ?', '1')->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        if (empty($result)) {
            $result = $result[0];
        }
        return $result;
    }


}
