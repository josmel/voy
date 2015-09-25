<?php

class Application_Model_DbTable_Empresa extends Core_Db_Table {

    protected $_name = 'Empresas';
    protected $_primary = "id";

    const NAMETABLE = 'Empresas';

    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
    }

    static function populate($params) {
        $data = array();
        if (isset($params['Name']))
            $data['Name'] = $params['Name'];
        if (isset($params['Email']))
            $data['Email'] = $params['Email'];
        if (isset($params['Phone']))
            $data['Phone'] = $params['Phone'];
        if (isset($params['Subject']))
            $data['Subject'] = $params['Subject'];
        if (isset($params['Date']))
            $data['Date'] = $params['Date'];
        return $data;
    }
    /**
     * 
     * @param obj DB $resulQuery
     */
 
    public function getPrimaryKey() {
        return $this->_primary;
    }

    public function getWhereActive() {
        return " AND estado != 2";
    }

}
