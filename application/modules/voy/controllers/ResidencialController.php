<?php

class Voy_ResidencialController extends App_Controller_ActionVoy {

    protected $_sessionPlan;

    public function init() {
        parent::init();
        $this->_Services = new App_Controller_Services();
        $this->view->fecha = $this->_Services->obtenerUltimoDiaMesActual();
        $this->_sessionPlan = new Zend_Session_Namespace('sessionPlan');
    }

    function obtenerDatosCobertura($cobertura, $plan, $predio, $direccion, $departamento, $provincia, $distrito, $persona) {
        $datosDePlan = $this->_Services->BuscarPlanPorCodigo($plan);
        switch ($cobertura) {
            case 1: //area de cobertura (azul)
                if (isset($predio)) {
                    $valorPredio = $predio;
                    $predioNuevo = 'no';
                } else {
                    if ($plan == 2) {//Negocios
                        $tipoPredio = 9;//Solo Teléfono
                    } else {
                        $tipoPredio = 8;//Otros
                    }
                    $address = $this->_Services->BuscarCoordendasPorDireccion($direccion);
                    $estado = $this->_Services->RegistrarPredio(0,$direccion,$direccion,1,'',$address['latitud'],$address['longitud'], 0, $departamento, $provincia, $distrito, $tipoPredio, 6);
                    $valorPredio = $estado['codigo'];
                    $predioNuevo = 'si';
                }
                switch ($predioNuevo) {
                    case 'si':
                        $mensaje = 'Verificaremos si su dirección dispone de factibilidad técnica para coordinar la instalaci&oacute;n de tu servicio.';
                        $estadoCliente = 17;//Dentro de Cobertura
                        $estadoContrato = 12;//Pre pendiente
                        break;
                    case 'no':
                        $mensaje = 'En breve nos comunicaremos contigo para coordinar la instalaci&oacute;n de tu servicio.';
                        $estadoCliente = 15;//Contratar por Web
                        $estadoContrato = 6;//Pendiente
                        break;
                    default:
                        break;
                }
                $estadoOrden = 7; //Pendiente
                $estado = $this->_Services->BuscarEstadosCliente($estadoCliente);
                if($estadoCliente==17){ //Dentro de Cobertura
                   $cobertura = 'Verificar factibilidad técnica de predio N° : ' . $valorPredio.'';
                }else{
                   $cobertura = 'Plan: ' . $datosDePlan['nombre'] . ' [' . $persona . '] -- Estado : ' . $estado['descripcion'];
                }
                $datos = array($mensaje, $cobertura, $estadoCliente, $valorPredio, $estado['descripcion'], $estadoContrato, $estadoOrden);
                break;
            case 2: //area de posible cobertura (amarillo)
                $mensaje = 'Pronto nos comunicaremos contigo cuando tú dirección disponga de cobertura, en breve lo estará y te llamaremos en cuanto puedas contratar nuestros servicios.';
                $estadoCliente = 16;//Posible Cobertura
                $estado = $this->_Services->BuscarEstadosCliente($estadoCliente);
                $cobertura = 'Plan: ' . $datosDePlan['nombre'] . ' [' . $persona . '] -- Estado : ' . $estado['descripcion'];
                $datos = array($mensaje, $cobertura, $estadoCliente, null, $estado['descripcion']);
                break;
            case 3: //area fuera de cobertura (fuera de area)
                $mensaje = 'Por el momento no disponemos de cobertura para tu dirección. Te llamaremos en cuanto puedas contratar nuestros servicios.';
                $estadoCliente = 14; //Fuera de Cobertura
                $estado = $this->_Services->BuscarEstadosCliente($estadoCliente);
                $cobertura = 'Plan: ' . $datosDePlan['nombre'] . ' [' . $persona . '] -- Estado : ' . $estado['descripcion'];
                $datos = array($mensaje, $cobertura, $estadoCliente, null, $estado['descripcion']);
                break;
            default:
                break;
        }
        return $datos;
    }

    function guardarPlanesResidencial($dataForm, $persona) {
        $datosCobertura = $this->obtenerDatosCobertura($this->_sessionPlan->cobertura, $dataForm['plan'], $this->_sessionPlan->predio, $dataForm["direccion"], $dataForm["departamento"], $dataForm["provincia"], $dataForm["distrito"], $persona);
        $datosDePlan = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
        $datoTipoPlan = $this->_Services->BuscarTipoPlan($datosDePlan['T_TipoPlan_codigo']);
        $Ubigeo = $this->_Services->ListarUbigeo($dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']);
        $dataForm['T_EstadoCliente_codigo'] = $datosCobertura[2];
        $dataForm['codigo'] = $this->consultaDNI($dataForm['documento']);
        $dataForm['apellido'] = $dataForm['apellidoPaterno'] . ' ' . $dataForm['apellidoMaterno'];
        $resultadoClienteNatural = $this->_Services->clienteNatural($dataForm);
        if ($this->_sessionPlan->cobertura == 1) {//area de cobertura (azul)
            $resultadoContrato = $this->_Services->RegistrarContrato(0, '', $dataForm["direccion"], date('Ymd'), '', $dataForm["direccion"], $resultadoClienteNatural['codigo'], $datosCobertura[5], $datosCobertura[3], '');
            $resultadoPlanesServicio = $this->_Services->ListarPlanesServiciosPorPlan($dataForm['plan']);
            foreach ($resultadoPlanesServicio as $key => $value) {
                $this->_Services->RegistrarDetalleContrato(0, $resultadoContrato['codigo'], date('Ymd'), $value['codigo']);
            }
            $this->_Services->RegistrarOrdenCliente(0, ucwords(strtolower($dataForm['nombre'])), $dataForm['fijo'], $dataForm['celular']
                    , $dataForm['correo'], 'Instalaciones', '', '', $resultadoContrato['codigo'], $datosCobertura[6]);
        }
        $sendMail = array(
            'documento' => $dataForm['documento'],
            'nombre' => ucwords(strtolower($dataForm['nombre'])),
            'apellido' => $dataForm['apellido'],
            'direccion' => $dataForm['direccion'],
            'distrito' => $Ubigeo,
            'fijo' => $dataForm['fijo'],
            'celular' => $dataForm['celular'],
            'correo' => $dataForm['correo'],
            'plan' => $datosDePlan['nombre'],
            'tipo' => $datoTipoPlan["descripcion"],
            'estado' => $datosCobertura[4],
            'mensaje' => $datosCobertura[0],
            'asunto' => $datosCobertura[1]
        );
        if (isset($dataForm['telefonia'])):
            $sendMail['telefonia'] = $dataForm['telefonia'];
        endif;
        switch ($dataForm['plan']) {
            case 1: //100 Megas
                if ($this->_sessionPlan->cobertura == 1) {
                    if ($datosCobertura[5] == 12) {// ESTADO CONTRATO-Pre pendiente
                         $sendMail['predio'] = $datosCobertura[3];
                        $this->_mailHelper->contratarpreviaAdmin($sendMail);
                    } else { //ESTADO CONTRATO-Pendiente
                        $this->_mailHelper->contratarAdmin($sendMail);
                    }
                } else {
                    $this->_mailHelper->contratarAdmin($sendMail);
                }
                $sendMail['to'] = $dataForm['correo'];
                $sendMail['asunto'] = 'Gracias por contactarte con nosotros';
                $this->_mailHelper->contratar($sendMail);
                break;
            case 3://100 Megas + Teléfono
                if ($this->_sessionPlan->cobertura == 1) {
                    if ($datosCobertura[5] == 12) {// ESTADO CONTRATO-Pre pendiente
                        $sendMail['predio'] = $datosCobertura[3];
                        $this->_mailHelper->contratarpreviaAdmin($sendMail);
                    } else {//ESTADO CONTRATO-Pendiente
                        $this->_mailHelper->contratartelefoniaAdmin($sendMail);
                    }
                } else {
                    $this->_mailHelper->contratartelefoniaAdmin($sendMail);
                }
                $sendMail['to'] = $dataForm['correo'];
                $sendMail['asunto'] = 'Gracias por contactarte con nosotros';
                $this->_mailHelper->contratartelefonia($sendMail);
                break;
            default:
                break;
        }
        unset($this->_sessionPlan->predio);
        unset($this->_sessionPlan->direccion);
        unset($this->_sessionPlan->plan);
        unset($this->_sessionPlan->cobertura);
        $this->messageEmail('residencial');
    }

    function guardarPlanesJuridico($dataForm, $persona) {
        $datosCobertura = $this->obtenerDatosCobertura($this->_sessionPlan->cobertura, $dataForm['plan'], $this->_sessionPlan->predio, $dataForm["direccion"], $dataForm["departamento"], $dataForm["provincia"], $dataForm["distrito"], $persona);
        $datosDePlan = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
        $datoTipoPlan = $this->_Services->BuscarTipoPlan($datosDePlan['T_TipoPlan_codigo']);
        $Ubigeo = $this->_Services->ListarUbigeo($dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']);
        $dataForm['T_EstadoCliente_codigo'] = $datosCobertura[2];
        $dataForm['codigo'] = $this->consultaNumeroRUC($dataForm['numeroRUC']);
        $resultadoClienteJuridico = $this->_Services->clienteJuridico($dataForm);
        if ($this->_sessionPlan->cobertura == 1) {
            $resultadoContrato = $this->_Services->RegistrarContrato(0, '', $dataForm["direccion"], date('Ymd'), '', $dataForm["direccion"], $resultadoClienteJuridico['codigo'], $datosCobertura[5], $datosCobertura[3], '');
            $resultadoPlanesServicio = $this->_Services->ListarPlanesServiciosPorPlan($dataForm['plan']);
            foreach ($resultadoPlanesServicio as $key => $value) {
                $this->_Services->RegistrarDetalleContrato(0, $resultadoContrato['codigo'], date('Ymd'), $value['codigo']);
            }
            $this->_Services->RegistrarOrdenCliente(0, ucwords(strtolower($dataForm['nombre'])), $dataForm['fijo'], $dataForm['celular']
                    , $dataForm['correo'], 'Alfonso Rosales', '', '', $resultadoContrato['codigo'], $datosCobertura[6]);
        }
        $sendMail = array(
            'fijo' => $dataForm['fijo'],
            'celular' => $dataForm['celular'],
            'correo' => $dataForm['correo'],
            'nombre' => $dataForm['nombreContacto'],
            'razonSocial' => $dataForm['razonSocial'],
            'direccion' => $dataForm['direccion'],
            'distrito' => $Ubigeo,
            'plan' => $datosDePlan['nombre'],
            'tipo' => $datoTipoPlan["descripcion"],
            'estado' => $datosCobertura[4],
            'telefonia' => $dataForm['telefonia'],
            'mensaje' => $datosCobertura[0],
            'asunto' => $datosCobertura[1]
        );
        if (isset($dataForm['telefonia'])):
            $sendMail['telefonia'] = $dataForm['telefonia'];
        endif;
        switch ($dataForm['plan']) {
            case 4://100 MEGAS + TELEFONO VOY NEGOCIOS
                if ($this->_sessionPlan->cobertura == 1) {
                    if ($datosCobertura[5] == 12) {// ESTADO CONTRATO-Pre pendiente
                        $sendMail['predio'] = $datosCobertura[3];
                        $this->_mailHelper->contratarpreviaAdmin($sendMail);
                    } else {//ESTADO CONTRATO-Pendiente
                         $this->_mailHelper->contratarnegociosAdmin($sendMail);
                    }
                } else {
                     $this->_mailHelper->contratarnegociosAdmin($sendMail);
                }
                $sendMail['to'] = $dataForm['correo'];
                $sendMail['asunto'] = 'Gracias por contactarte con nosotros';
                $this->_mailHelper->contratarnegocios($sendMail);
                break;
            default:
                $this->_mailHelper->contratarnegociosAdmin($sendMail);
                $sendMail['to'] = $dataForm['correo'];
                $sendMail['asunto'] = 'Gracias por contactarte con nosotros';
                $this->_mailHelper->contratarnegocios($sendMail);
                break;
        }
        unset($this->_sessionPlan->predio);
        unset($this->_sessionPlan->direccion);
        unset($this->_sessionPlan->plan);
        unset($this->_sessionPlan->cobertura);
        $this->messageEmail('negocios');
    }

    public function residencialAction() {
//        unset($this->_sessionPlan->predio);
//        unset($this->_sessionPlan->direccion);
//        unset($this->_sessionPlan->plan);
//        unset($this->_sessionPlan->cobertura);
        if (isset($this->_sessionPlan->predio) || isset($this->_sessionPlan->direccion) || isset($this->_sessionPlan->cobertura)) {
            if (isset($this->_sessionPlan->predio)):
                $this->view->predio = $this->_sessionPlan->predio;
            endif;
            if (isset($this->_sessionPlan->cobertura)):
                $this->view->cobertura = $this->_sessionPlan->cobertura;
            endif;
            if (isset($this->_sessionPlan->direccion)):
                $this->view->direccion = $this->_sessionPlan->direccion;
            endif;
            $this->view->flujoResidencial = 1;
        }
        else {
            $this->view->flujoResidencial = 0;
        }
        $ListarPlanesResidencial = $this->_Services->ListarPlanes(1);
        $TipoPlan = $this->_Services->BuscarTipoPlan(1);
        $this->view->TipoPlan = $TipoPlan['legal'];
        $this->view->ListarPlanesResidencial = $ListarPlanesResidencial;
    }

    public function contratarAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            
//            var_dump($dataForm);exit;
            try {
                if (isset($dataForm['source']) && $dataForm['source'] = 'map') {
                    if (isset($dataForm['predio'])) {
                        $this->_sessionPlan->predio = $dataForm['predio'];
                        $this->view->predio = $this->_sessionPlan->predio;
                    }
                    if (isset($dataForm['direccion'])):
                        $this->_sessionPlan->direccion = $dataForm['direccion'];
                    endif;
                    $this->_sessionPlan->cobertura = $dataForm['cobertura'];
                    if ($dataForm['plan'] == 0) {
                        $this->_redirect('/residencial');
                        exit;
                    } else {
                        $this->_sessionPlan->plan = $dataForm['plan'];
                        if (isset($this->_sessionPlan->predio)) {
                            $datosPredio = $this->_Services->BuscarPredio($this->_sessionPlan->predio);
                            $departamento = $datosPredio['departamento'];
                            $provincia = $datosPredio['provincia'];
                            $distrito = $datosPredio['distrito'];
                            $datosGeneralesPredio = array('departamento' => $departamento,
                                'provincia' => $provincia,
                                'distrito' => $distrito,
                                'direccion' => $datosPredio['nombre'] . ', ' . $datosPredio['direccion']
                            );
                        }
                        if (isset($this->_sessionPlan->direccion)) {
                            $departamento = 15;//Lima
                            $provincia = 1;//Lima
                            $distrito = 40;//Sqntiago de surco
                            $datosGeneralesPredio = array('departamento' => $departamento,
                                'provincia' => $provincia,
                                'distrito' => $distrito,
                                'direccion' => $this->_sessionPlan->direccion
                            );
                        }
                        $this->view->datosPredio = $datosGeneralesPredio;
                        $this->view->ListarDepartamentos = $this->_Services->ListarDepartamentos();
                        $this->view->plan = $this->_sessionPlan->plan;
                        $this->view->ListarProvincias = $this->_Services->ListarProvincias($departamento);
                        $this->view->ListarDistritos = $this->_Services->ListarDistritos($departamento, $provincia);
                        $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
                        $this->view->DetallePlan = $BuscarPlanPorCodigo;
                    }
                } else {
                    if (isset($dataForm['plan'])) {
                        $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
                        if ($BuscarPlanPorCodigo['T_TipoPlan_codigo'] == 1) {
                            $this->guardarPlanesResidencial($dataForm, 'Residencial');
                        } else {
                            $this->guardarPlanesJuridico($dataForm, 'Juridico');
                        }
                    } else {
                        
                    }
                }
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        }
        $this->view->ListarTiposDocumento = $this->_Services->ListarTiposDocumento();
    }

    public function reservarAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                if (isset($dataForm['source']) && $dataForm['source'] = 'map') {
                    if (isset($dataForm['predio'])) {
                        $this->_sessionPlan->predio = $dataForm['predio'];
                        $this->view->predio = $this->_sessionPlan->predio;
                    }
                    $this->_sessionPlan->cobertura = $dataForm['cobertura'];
                    if ($dataForm['plan'] == 0) {
                        if (isset($dataForm['direccion'])):

                            $this->_sessionPlan->direccion = $dataForm['direccion'];
                        endif;
                        $this->_redirect('/residencial');
                        exit;
                    } else {
                        $this->_sessionPlan->plan = $dataForm['plan'];
                        if (isset($dataForm['direccion'])) {
                            $departamento = 15;//Lima
                            $provincia = 1;//Lima
                            $distrito = 40;//Sqntiago de surco
                            $datosGeneralesPredio = array('departamento' => $departamento,
                                'provincia' => $provincia,
                                'distrito' => $distrito,
                                'direccion' => $dataForm['direccion']
                            );
                        }
                        $this->view->datosPredio = $datosGeneralesPredio;
                        $this->view->plan = $this->_sessionPlan->plan;
                        $this->view->ListarDepartamentos = $this->_Services->ListarDepartamentos();
                        $this->view->ListarProvincias = $this->_Services->ListarProvincias($departamento);
                        $this->view->ListarDistritos = $this->_Services->ListarDistritos($departamento, $provincia);
                        $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
                        $this->view->DetallePlan = $BuscarPlanPorCodigo;
                        $this->view->cobertura = $dataForm['cobertura'];
                    }
                } else {

                    if (isset($dataForm['plan'])) {
                        $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
                        if ($BuscarPlanPorCodigo['T_TipoPlan_codigo'] == 1) {
                            $this->guardarPlanesResidencial($dataForm, 'Residencial');
                        } else {
                            $this->guardarPlanesJuridico($dataForm, 'Juridico');
                        }
                    } else {
                        
                    }
                }
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        }
        $this->view->ListarTiposDocumento = $this->_Services->ListarTiposDocumento();
        $this->view->ListarDepartamentos = $this->_Services->ListarDepartamentos();
        $this->view->ListarProvincias = $this->_Services->ListarProvincias(15);
        $this->view->ListarDistritos = $this->_Services->ListarDistritos(15, 1);
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

    public function contratarTelefoniaAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                $datosDePlan = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
                $datoTipoPlan = $this->_Services->BuscarTipoPlan($datosDePlan['T_TipoPlan_codigo']);
                $dataForm['codigo'] = $this->consultaDNI($dataForm['documento']);
                $Ubigeo = $this->_Services->ListarUbigeo($dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']);
                $dataForm['T_EstadoCliente_codigo'] = 15;//Contratar por Web
                $estadoContrato = 6;//Pendiente
                $estadoOrden = 7;//Pendiente
                $estado = $this->_Services->BuscarEstadosCliente($dataForm['T_EstadoCliente_codigo']);
                $dataForm['apellido'] = $dataForm['apellidoPaterno'] . ' ' . $dataForm['apellidoMaterno'];
                $sendMail = array(
                    'telefonia' => $dataForm['telefonia'],
                    'documento' => $dataForm['documento'],
                    'nombre' => $dataForm['nombre'],
                    'apellido' => $dataForm['apellido'],
                    'direccion' => $dataForm['direccion'],
                    'distrito' => $Ubigeo,
                    'fijo' => $dataForm['fijo'],
                    'celular' => $dataForm['celular'],
                    'correo' => $dataForm['correo'],
                    'plan' => $datosDePlan['nombre'],
                    'estado' => $estado['descripcion'],
                    'tipo' => $datoTipoPlan["descripcion"],
                    'asunto' => $cobertura = 'Plan: ' . $datosDePlan['nombre'] . ' [Residencial] -- Estado : ' . $estado['descripcion']
                );

                $this->_mailHelper->contratartelefoniaAdmin($sendMail);
                $sendMail['to'] = $dataForm['correo'];
                $sendMail['asunto'] = 'Gracias por contactarte con nosotros';
                $this->_mailHelper->contratartelefonia($sendMail);
                $resultadoClienteNatural = $this->_Services->clienteNatural($dataForm);

            //predio por defecto 132
                $datosCobertura = $this->obtenerDatosCobertura(1, $dataForm['plan'], 132, $dataForm["direccion"], $dataForm["departamento"], $dataForm["provincia"], $dataForm["distrito"]);
                $resultadoContrato = $this->_Services->RegistrarContrato(0, '', $dataForm["direccion"], date('Ymd'), '', $dataForm["direccion"], $resultadoClienteNatural['codigo'], $estadoContrato, $datosCobertura[3], '');
                $resultadoPlanesServicio = $this->_Services->ListarPlanesServiciosPorPlan($dataForm['plan']);
                foreach ($resultadoPlanesServicio as $key => $value) {
                    $this->_Services->RegistrarDetalleContrato(0, $resultadoContrato['codigo'], date('Ymd'), $value['codigo']);
                }
                $this->_Services->RegistrarOrdenCliente(0, ucwords(strtolower($dataForm['nombre'])), $dataForm['fijo'], $dataForm['celular']
                        , $dataForm['correo'], 'Alfonso Rosales', '', $resultadoContrato['codigo'], $estadoOrden);

                $this->messageEmail('residencial');
                unset($this->_sessionPlan->predio);
                unset($this->_sessionPlan->direccion);
                unset($this->_sessionPlan->plan);
                unset($this->_sessionPlan->cobertura);
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            $request = $this->getRequest();
            $plan = $request->getParam('plan');
            $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo($plan);
            $this->view->plan = $plan;
            $this->view->DetallePlan = $BuscarPlanPorCodigo;
            $this->view->ListarTiposDocumento = $this->_Services->ListarTiposDocumento();
            $this->view->ListarDepartamentos = $this->_Services->ListarDepartamentos();
            $this->view->ListarProvincias = $this->_Services->ListarProvincias(15);
            $this->view->ListarDistritos = $this->_Services->ListarDistritos(15, 1);
        }
    }

}
