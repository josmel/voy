<?php

class Application_Model_DbTable_Cliente extends Core_Db_Table {

    protected $_name = 'clientes';
    protected $_primary = "id";

    const NAMETABLE = 'clientes';

    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
    }

    static function populate($params) {
        $data = array();
        if (isset($params['tipo']))
            $data['tipo'] = $params['tipo'];
        if (isset($params['nombre']))
            $data['nombre'] = $params['nombre'];
        if (isset($params['apellido']))
            $data['apellido'] = $params['apellido'];
        if (isset($params['documento']))
            $data['documento'] = $params['documento'];
        if (isset($params['direccion']))
            $data['direccion'] = $params['direccion'];
        if (isset($params['fijo']))
            $data['fijo'] = $params['fijo'];
        if (isset($params['celular']))
            $data['celular'] = $params['celular'];
        if (isset($params['correo']))
            $data['correo'] = $params['correo'];
        if (isset($params['fecha']))
            $data['fecha'] = $params['fecha'];
        if (isset($params['estado']))
            $data['estado'] = $params['estado'];
        if (isset($params['servicio']))
            $data['servicio'] = $params['servicio'];
        if (isset($params['telefonia']))
            $data['telefonia'] = $params['telefonia'];
        if (isset($params['distrito']))
            $data['distrito'] = $params['distrito'];
        if (isset($params['origen']))
            $data['origen'] = $params['origen'];
        if (isset($params['instalacion']))
            $data['instalacion'] = $params['instalacion'];
        
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
