<?php

class App_Controller_Services {

    protected $_config;
    public function __construct() {
        $this->_config = Zend_Registry::get('config');
    }

    public function SoapClient($wsdl) {
        return $objTable = new SoapClient($this->_config['app']['soap'] . '/' . $wsdl . '?wsdl', array("soap_version" => SOAP_1_2));
    }

    public function clienteNatural($dataForm) {

        $this->_SoapClient = $this->SoapClient('ClientesNaturalesService');
        try {
            $result = $this->_SoapClient->RegistrarClienteNatural(
                    $dataForm['codigo'], strtoupper($dataForm['apellidoPaterno']), strtoupper($dataForm['apellidoMaterno']), strtoupper($dataForm['nombre']), $dataForm['documento'], $dataForm['tipoDocumentoCodigo'], strtoupper($dataForm['direccion']), strtoupper($dataForm['direccion']), strtoupper($dataForm['direccion']), strtoupper($dataForm['direccion']), $dataForm['celular'], $dataForm['fijo'], strtoupper($dataForm['correo']), strtoupper($dataForm['correo']), 'observaciones', $dataForm['T_EstadoCliente_codigo'], $dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']
            );
            return $result["ResponseRegistrarClienteNaturalBE"]['clienteNatural'];
        } catch (SoapFault $e) {
            $this->_flashMessage->error($e->getMessage());
        }
    }

    public function ListarPlanesServiciosPorPlan($codigoPlan) {

        $this->_SoapClient = $this->SoapClient('PlanesServiciosService');
        try {
            $result = $this->_SoapClient->ListarPlanesServiciosPorPlan($codigoPlan);
            return $result["ResponseListarPlanesServiciosPorPlanBE"]['Plan'];
        } catch (SoapFault $e) {
            $this->_flashMessage->error($e->getMessage());
        }
    }

    public function RegistrarContrato($codigo, $numeroContrato, $ubicacionContratoFisico, $fechaInicioContrato, $fechaFinalContrato, $direccionExactaPredio, $codigoCliente, $codigoEstadoContrato, $codigoPredio,$observaciones) {
        $this->_SoapClient = $this->SoapClient('ContratosService');
        try {
            $result = $this->_SoapClient->RegistrarContrato($codigo, $numeroContrato, $ubicacionContratoFisico, $fechaInicioContrato, $fechaFinalContrato, strtoupper($direccionExactaPredio), $codigoCliente, $codigoEstadoContrato, $codigoPredio,strtoupper($observaciones));
            return $result["ResponseRegistrarContratoBE"]['Contrato'];
        } catch (SoapFault $e) {
            $this->_flashMessage->error($e->getMessage());
        }
    }

    public function RegistrarDetalleContrato($cargoInstalacion, $codigoContrato, $fechaEntrega, $codigoPlanServicio) {

        $this->_SoapClient = $this->SoapClient('DetallesContratoService');
        try {
            $result = $this->_SoapClient->RegistrarDetalleContrato($cargoInstalacion, $codigoContrato, $fechaEntrega, $codigoPlanServicio);
            return $result["ResponseRegistrarDetalleContratoBE"]['DetalleContrato'];
        } catch (SoapFault $e) {
            $this->_flashMessage->error($e->getMessage());
        }
    }

    public function RegistrarOrdenCliente($codigo, $nombreContactoInstalacion, $telefonoFijoContactoInstalacion, $telefonoCelularContactoInstalacion
    , $correoContactoInstalacion, $encargado, $rango,$observaciones, $codigoContrato, $codigoEstadoOrdenCliente) {

        $this->_SoapClient = $this->SoapClient('OrdenesClienteService');
        try {
            $result = $this->_SoapClient->RegistrarOrdenCliente($codigo, strtoupper($nombreContactoInstalacion), $telefonoFijoContactoInstalacion, $telefonoCelularContactoInstalacion
                    , strtoupper($correoContactoInstalacion),strtoupper($encargado),$rango, strtoupper($observaciones), $codigoContrato, $codigoEstadoOrdenCliente);
            return $result["ResponseRegistrarOrdenClienteBE"]['Orden'];
        } catch (SoapFault $e) {
            $this->_flashMessage->error($e->getMessage());
        }
    }

    public function clienteJuridico($dataForm) {
        $this->_SoapClient = $this->SoapClient('ClientesJuridicosService');
        try {
            $result = $this->_SoapClient->RegistrarClienteJuridico(
                    $dataForm['codigo'],strtoupper($dataForm['razonSocial']), $dataForm['numeroRUC'], strtoupper($dataForm['nombreContacto']), strtoupper($dataForm['direccion']), strtoupper($dataForm['direccion']), strtoupper($dataForm['direccion']), strtoupper($dataForm['direccion']), $dataForm['celular'], $dataForm['fijo'], strtoupper($dataForm['correo']), strtoupper($dataForm['correo']), strtoupper($dataForm['Subject']), $dataForm['T_EstadoCliente_codigo'], $dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']);
            return $result["ResponseRegistrarClienteJuridicoBE"]['clienteJuridico'];
        } catch (SoapFault $e) {
            $this->_flashMessage->error($e->getMessage());
        }
    }

    function ListarClienteNatural($documentoIdentidad) {
        $this->_SoapClient = $this->SoapClient('ClientesNaturalesService');
        $result = $this->_SoapClient->ListarClienteNatural('', '', '', $documentoIdentidad);
        return $result["ResponseListarClienteNaturalBE"];
    }

    function ListarClienteJuridico($numeroRUC) {
        $this->_SoapClient = $this->SoapClient('ClientesJuridicosService');
        $result = $this->_SoapClient->ListarClienteJuridico('', '', $numeroRUC);
        return $result["ResponseListarClienteJuridicoBE"];
    }

    function ListarDepartamentos() {
        $this->_SoapClient = $this->SoapClient('UbigeoService');
        $result = $this->_SoapClient->ListarDepartamentos(0);
        return $result["ResponseListarDepartamentosBE"]['Departamentos'];
    }

    function ListarProvincias($departamento) {
        $this->_SoapClient = $this->SoapClient('UbigeoService');
        $result = $this->_SoapClient->ListarProvincias($departamento, 0);
        return $result["ResponseListarProvinciasBE"]['Provincias'];
    }

    function ListarDistritos($departamento, $provincia) {
        $this->_SoapClient = $this->SoapClient('UbigeoService');
        $result = $this->_SoapClient->ListarDistritos($departamento, $provincia, 0);
        return $result["ResponseListarDistritosBE"]['Distritos'];
    }

    function ListarPredios($a, $b, $c, $d) {
        $this->_SoapClient = $this->SoapClient('PrediosService');
        $result = $this->_SoapClient->ListarPredios($a, $b, $c, $d);
        return $result["ResponseListarPrediosBE"]['ListarPredios'];
    }

    function BuscarPredio($codigo) {
        $this->_SoapClient = $this->SoapClient('PrediosService');
        $result = $this->_SoapClient->BuscarPredio($codigo);
        return $result["ResponseBuscarPredioBE"];
    }

    function RegistrarPredio($codigo, $nombre, $direccion, $numeroEspacios, $capacidadSplitter, $latitud, $longitud, $tieneONTenRecepcion, $departamento, $provincia, $distrito, $codigoTipoPredio, $codigoEstadoPredio) {
        $this->_SoapClient = $this->SoapClient('PrediosService');
        $result = $this->_SoapClient->RegistrarPredio($codigo, strtoupper($nombre), strtoupper($direccion), $numeroEspacios, $capacidadSplitter, $latitud, $longitud, $tieneONTenRecepcion, $departamento, $provincia, $distrito, $codigoTipoPredio, $codigoEstadoPredio);
        return $result["ResponseRegistrarPredioBE"]['Predio'];
    }

    function ListarUbigeo($departamento, $provincia, $distrito) {
        $this->_SoapClient = $this->SoapClient('UbigeoService');
        $result = $this->_SoapClient->ListarUbigeo($departamento, $provincia, $distrito);
        return $result["ResponseListarUbigeoBE"]['Ubigeo'][0]['descripcion'];
    }

    function ListarPlanes($tipoPlan) {
        $this->_SoapClient = $this->SoapClient('PlanesService');
        $result = $this->_SoapClient->ListarPlanes($tipoPlan);
        return $result["ResponseListarTipoPlanBE"]['Planes'];
    }

    function BuscarPlanPorCodigo($codigo) {
        $this->_SoapClient = $this->SoapClient('PlanesService');
        $result = $this->_SoapClient->BuscarPlanPorCodigo($codigo);
        return $result["ResponseBuscarTipoPlanBE"]['plan'];
    }

    function BuscarEstadosCliente($codigo) {
        $this->_SoapClient = $this->SoapClient('EstadosClienteService');
        $result = $this->_SoapClient->BuscarEstadosCliente($codigo);
        return $result["ResponseBuscarEstadosClienteBE"];
    }

    function BuscarTipoPlan($codigo) {
        $this->_SoapClient = $this->SoapClient('TiposPlanService');
        $result = $this->_SoapClient->BuscarTipoPlan($codigo);
        return $result["ResponseBuscarTipoPlanBE"];
    }

    function ListarTiposDocumento() {
        $this->_SoapClient = $this->SoapClient('TiposDocumentoService');
        $result = $this->_SoapClient->ListarTiposDocumento();
        return $result["ResponseListarTiposDocumentoBE"]['TiposDocumento'];
    }

    function BuscarCoordendasPorDireccion($address) {
        $this->_SoapClient = $this->SoapClient('CoberturasService');
        $result = $this->_SoapClient->BuscarCoordendasPorDireccion($address);
        return $result["ResponseBuscarCoordendasPorDireccionBE"]['resultadoCoordenadas'];
    }

    function BuscarCoberturaPorLongitudLatitud($latitud, $longitud) {
        $this->_SoapClient = $this->SoapClient('CoberturasService');
        $result = $this->_SoapClient->BuscarCoberturaPorLongitudLatitud($latitud, $longitud);
        return $result["ResponseBuscarCoberturaPorLongitudLatitudBE"]['resultadoCobertura'];
    }

    function MapaDeCobertura() {
        $this->_SoapClient = $this->SoapClient('CoberturasService');
        $result = $this->_SoapClient->MapaDeCobertura();
        return $result["ResponseMapaDeCoberturaBE"]['mapa'];
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
