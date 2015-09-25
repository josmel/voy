<?php

class Voy_CoberturaController extends App_Controller_ActionVoy {

    protected $_sessionPlan;

    public function init() {
        parent::init();
        $this->_Services = new App_Controller_Services();
        $this->view->fecha = $this->_Services->obtenerUltimoDiaMesActual();
        $this->_sessionPlan = new Zend_Session_Namespace('sessionPlan');
    }

    public function indexAction() { 
        $polygons = $this->_Services->MapaDeCobertura();
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                if (isset($dataForm["address"])) {
                    $address = $this->_Services->BuscarCoordendasPorDireccion($dataForm["address"]);
                    if ($address['latitud'] == 0) {
                        $this->view->plan = $this->_sessionPlan->plan;
                        $this->view->errorBusqueda = 1;
                        $this->view->address_x = $this->_config['app']['latitud'];
                        $this->view->address_y = $this->_config['app']['longitud'];
                    } else {
                        $BuscarCobertura = $this->_Services->BuscarCoberturaPorLongitudLatitud($address['latitud'], $address['longitud']);
                        if (isset($this->_sessionPlan->plan)) {
                            $this->view->plan = $this->_sessionPlan->plan;
                        }
                        $this->view->estado = $BuscarCobertura["estado"];
                        $this->view->area = $BuscarCobertura["area"];
                        $this->view->result = $BuscarCobertura["result"];
                        $this->view->address_x = $BuscarCobertura["latitud"];
                        $this->view->address_y = $BuscarCobertura["longitud"];
                        $this->view->got_this_address = $dataForm["address"];
                    }
                }
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            unset($this->_sessionPlan->plan);
            $request = $this->getRequest();
            $plan = $request->getParam('plan');
            $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo($plan);
            if ($BuscarPlanPorCodigo) {
                $this->_sessionPlan->plan = $plan;
            } else {
                $this->_sessionPlan->plan = 0;
            }
            $this->view->plan = $this->_sessionPlan->plan;
            $this->view->address_x = $this->_config['app']['latitud'];
            $this->view->address_y = $this->_config['app']['longitud'];
        }
        $this->view->polygons = $polygons;
    }

    public function buscarDireccionPredioAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_getParam('direccion', 0);
            try {
                $ListarPredios = $this->_Services->ListarPredios('', $dataForm, 0, 0);
                foreach ($ListarPredios as $key => $value) {
                    $valor[] = array('direccion' => $value['direccion'],
                        'postcode' => $value['codigo'], 'nombre' => $value['nombre']
                    );
                }
                echo json_encode(array('localities' => array('locality' => $valor)));
                exit;
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            echo 'acceso denegado';
            exit;
        }
    }

    public function edificiosVestidosAction() {
        $ListarPredios = $this->_Services->ListarPredios('', '', 0, 2);
        foreach ($ListarPredios as $value) {
            $key[] = array(
                'type' => "Feature",
                'geometry' => array("type" => "Point",
                    'coordinates' => array(
                        $value['longitud'],
                        $value['latitud']
                    )
                ),
                'properties' => array(
                    "codigo" => $value['codigo'],
                    "direccion" => $value['direccion'],
                    "nombre" => $value['nombre'],
                    "descripcionEstado" => $value["descripcionEstado"],
                    "T_EstadoPredio_codigo" => $value["T_EstadoPredio_codigo"],
                    "T_TipoPredio_codigo" => $value['T_TipoPredio_codigo'],
                    "descripcionPredio" => $value["descripcionPredio"],
                    "valor" => 1
                )
            );
        }
        $dato['type'] = "FeatureCollection";
        $dato['features'] = $key;
        echo (json_encode($dato));
        exit;
    }

}
