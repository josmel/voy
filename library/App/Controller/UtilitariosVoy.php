<?php

include_once APPLICATION_SOLR . '/library/SolrPhpClient/Apache/Solr/Service.php';

/**
 *
 * @author jyupanqui
 *        
 */
class App_Controller_UtilitariosVoy {

    protected $_config;
    protected $_flashMessage;
    protected $_mailHelper;

    /**
     */
    function __construct() {
        $this->_config = Zend_Registry::get('config');
        $this->_flashMessage = new Core_Controller_Action_Helper_FlashMessengerCustom();
        $this->_mailHelper = $this->_helper->getHelper('Mail');
    }

    public function consultaDNI($dni) {
        $Data = $this->_Services->ListarClienteNatural($dni);
        if ($Data["estado"] == 1 && count($Data["ClientesNatural"]) == 1) {
            return (int) $Data["ClientesNatural"][0]["codigo"];
        } else {
            return 0;
        }
    }

    public function consultaNumeroRUC($numeroRUC) {
        $Data = $this->_Services->ListarClienteJuridico($numeroRUC);
        if ($Data["estado"] == 1 && count($Data["ClientesJuridico"]) == 1) {
            return (int) $Data["ClientesJuridico"][0]["codigo"];
        } else {
            return 0;
        }
    }

    function validaSelect($selectDestino) {
        // Se valida que el select enviado via GET exista
        $listadoSelects = array(
            "departamento" => "ListarDepartamentos",
            "provincia" => "ListarProvincias",
            "distrito" => "ListarDistritos"
        );
        if (isset($listadoSelects[$selectDestino]))
            return true;
        else
            return false;
    }

    function validaOpcion($opcionSeleccionada) {
        // Se valida que la opcion seleccionada por el usuario en el select tenga un valor numerico
        if (is_numeric($opcionSeleccionada))
            return true;
        else
            return false;
    }

    function obtenerUltimoDiaMesActual() {
        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $dia = date("d", (mktime(0, 0, 0, date('m') + 1, 1, date('Y')) - 1));
        return $dia . " de " . $arrayMeses[date('m') - 1] . " del " . date('Y');
    }

}
