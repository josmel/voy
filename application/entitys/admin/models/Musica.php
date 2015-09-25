<?php

class Admin_Model_Musica extends Core_Model {

    protected $_tableMusica;

    public function __construct() {
        $this->_tableMusica = new Application_Model_DbTable_Musica();
    }

    public function idImg($id) {
        $smt = $this->_tableMusica->getAdapter()->select()
                        ->from($this->_tableMusica->getName(), array('img'))
                        ->where("idmusica = ?", $id)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }

    public function eliminarBanner($id) {
        $where = $this->_tableMusica->getAdapter()
                ->quoteInto('idmusica = ?', $id);
        $this->_tableMusica->delete($where);
    }
    
    public function MusicaAll() {
        $select = $this->_tableMusica->getAdapter()->select()
                        ->from($this->_tableMusica->getName(), array('titulo', 'url', 'img'))
                        ->where('estado LIKE ?', '1')->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        if (empty($result)) {
            $result = $result[0];
        }
        return $result;
    }



}
