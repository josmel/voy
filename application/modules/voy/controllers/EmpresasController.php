<?php

class Voy_EmpresasController extends App_Controller_ActionVoy {

    protected $_sessionPlan;

    public function init() {
        parent::init();
        $this->_Services = new App_Controller_Services();
        $this->view->fecha = $this->_Services->obtenerUltimoDiaMesActual();
        $this->_sessionPlan = new Zend_Session_Namespace('sessionPlan');
    }

    public function empresasAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                $datosDePlan = $this->_Services->BuscarPlanPorCodigo($dataForm['plan']);
                $datoTipoPlan = $this->_Services->BuscarTipoPlan($datosDePlan['T_TipoPlan_codigo']);
                $Ubigeo = $this->_Services->ListarUbigeo($dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']);
                $mensaje = 'Te llamaremos en cuanto puedas contratar nuestros servicios.';
                $cobertura = 'Esta en el área fuea de cobertura , Se registro como reservado';
                $dataForm['T_EstadoCliente_codigo'] = 10;
                $estado = $this->_Services->BuscarEstadosCliente($dataForm['T_EstadoCliente_codigo']);
                $dataForm['codigo'] = $this->consultaNumeroRUC($dataForm['numeroRUC']);
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
                    'estado' => $estado['descripcion'],
                    'asunto' => 'Solicitud de servicio corporativo',
//                    'telefonia' => $dataForm['telefonia'],
                     'Subject' =>$dataForm['Subject'],
                    'mensaje' => $mensaje
                );
                
                $this->_mailHelper->empresaAdmin($sendMail);
                $sendMail['to'] = $dataForm['correo'];
                $sendMail['asunto'] = 'Gracias por contactarte con nosotros';
                $this->_mailHelper->empresa($sendMail);
                
                $this->_Services->clienteJuridico($dataForm);
                $this->messageEmail('empresas');
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            $this->view->ListarDepartamentos = $this->_Services->ListarDepartamentos();
            $this->view->ListarProvincias = $this->_Services->ListarProvincias(15);
            $this->view->ListarDistritos = $this->_Services->ListarDistritos(15, 1);
            $TipoPlan = $this->_Services->BuscarTipoPlan(3);
            $this->view->TipoPlan = $TipoPlan['legal'];
            $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo(7);
            $this->view->DetallePlan = $BuscarPlanPorCodigo;
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

}
