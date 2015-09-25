<?php

class Admin_Model_AclRole extends Core_Model {

    protected $_tableAclRole;

    public function __construct() {
        $this->_tableAclRole = new Application_Model_DbTable_AclRole();
    }

    public function insertAclRole($id, $datas) {
        $data = array();
        for ($i = 0; $i < count($datas); $i++) {
            $data['idacl'] = $datas[$i];
            $data['idrol'] = $id;
            $this->_tableAclRole->insert($data);
        }
    }

    public function getFeaturedReserva($idReserva) {
        $smt = $this->_tableAclRole->getAdapter()->select()
                        ->from($this->_tableAclRole->getName())
                        ->where("tareserva_idreservation = ?", $idReserva)->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }

    public function getFeaturedReserva2($idReserva) {
        $smt = $this->_tableAclRole->getAdapter()->select()
                        ->from($this->_tableAclRole->getName())
                        ->where("tareserva_idreservation = ?", $idReserva)->query();

        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['idgreat']] = $row['great_spanish'];
        }
        $smt->closeCursor();
        return $result;
    }

    public function getFeatured($id) {
        $smt = $this->_tableAclRole->getAdapter()->select()->distinct()
                ->from(array('fh' => $this->_tableAclRole->getName()),
              array('idgreat' => 'fh.Featured_idgreat')
                )
                ->join(array('f' => 'Featured'), "f.idgreat = fh.Featured_idgreat", array('picture_great' => 'f.picture_great','great_spanish' => 'f.great_spanish', 'great_english' => 'f.great_english')
                )
                ->where("fh.tareserva_idreservation = ?", $id)
                ->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }

     public function getRoleAcl($id) {
        $smt = $this->_tableAclRole->getAdapter()->select()->distinct()
                ->from(array('fh' => $this->_tableAclRole->getName()),
              array('idacl' => 'fh.idacl')
                )
                ->join(array('f' => 'tacl'), "f.idacl = fh.idacl",array('idacl' => 'f.idacl')
                )
                ->where("fh.idrol = ?", $id)
                ->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }
    
    
    
    public function deletRole($id) {
      
    $where = $this->_tableAclRole->getAdapter()
                          ->quoteInto('idrol = ?', $id);
       $this->_tableAclRole->delete($where);
    }


}

