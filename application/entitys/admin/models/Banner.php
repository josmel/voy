<?php

class Admin_Model_Banner extends Core_Model {

    protected $_tableBanner;

    public function __construct() {
        $this->_tableBanner = new Application_Model_DbTable_Banner();
    }

    public function idImg($id) {
        $smt = $this->_tableBanner->getAdapter()->select()
                        ->from($this->_tableBanner->getName(), array('nombre'))
                        ->where("idbanner = ?", $id)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }

    public function eliminarBanner($id) {
        $where = $this->_tableBanner->getAdapter()
                ->quoteInto('idbanner = ?', $id);
        $this->_tableBanner->delete($where);
    }

    public function BannersAll() {
        $select = $this->_tableBanner->getAdapter()->select()
                        ->from($this->_tableBanner->getName(), array('nombre', 'descripcion', 'enlace', 'alt', 'posicion'))
                        ->where('estado LIKE ?', '1')->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        if (empty($result)) {
            $result = $result[0];
        }
        return $result;
    }

}
