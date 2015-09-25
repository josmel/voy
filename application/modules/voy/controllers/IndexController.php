<?php

//include_once APPLICATION_SOLR . '/library/SolrPhpClient/Apache/Solr/Service.php';

class Voy_IndexController extends App_Controller_ActionVoy {

    protected $_sessionPlan;

    public function init() {
        parent::init();
        $this->_Services = new App_Controller_Services();
        $this->view->fecha = $this->_Services->obtenerUltimoDiaMesActual();
        $this->_sessionPlan = new Zend_Session_Namespace('sessionPlan');
    }

    public function indexAction() {

//        $limit = 10;
//        $query = 'nombre: "PANORAMA GOLF"';
//        $results = false;
////        SolrPhpClient_
//        $solr = new Apache_Solr_Service('localhost', 8080, '/solr/predio');
//
//        // if magic quotes is enabled then stripslashes will be needed
//        if (get_magic_quotes_gpc() == 1) {
//            $query = stripslashes($query);
//        }
//
//        // in production code you'll always want to use a try /catch for any
//        // possible exceptions emitted  by searching (i.e. connection
//        // problems or a query parsing error)
//        try {
//            $results = $solr->search($query, 0, $limit);
//            if ($results) {
//                $total = (int) $results->response->numFound;
//                $start = min(1, $total);
//                $end = min($limit, $total);
//            }
//            var_dump($results->response->docs[0]);exit;
//        } catch (Exception $e) {
//            // in production you'd probably log or email this error to an admin
//            // and then show a special message to the user but for this example
//            // we're going to show the full exception
//            die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
//            exit;
//        }
    }


   

    public function contratarDuoAction() {
        if ($this->_request->isPost()) {
            $obj = new Application_Entity_RunSql('Cliente');
            $dataForm = $this->_request->getPost();
            try {
                $dataForm['codigo'] = $this->consultaDNI($dataForm['documento']);
                $Ubigeo = $this->_Services->ListarUbigeo($dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']);
                $dataForm['fecha'] = date('Y-m-d H:i:s');
                $dataForm['tipo'] = 'contrato';
                $dataForm['servicio'] = 'duo';
                $dataForm['estado'] = 'pendiente';
                $obj->save = $dataForm;
                $sendMail = array(
                    'documento' => $dataForm['documento'],
                    'nombre' => $dataForm['nombre'],
                    'apellido' => $dataForm['apellido'],
                    'direccion' => $dataForm['direccion'],
                    'distrito' => $Ubigeo,
                    'fijo' => $dataForm['fijo'],
                    'celular' => $dataForm['celular'],
                    'correo' => $dataForm['correo'],
                    'tipo' => $dataForm['tipo'],
                );
                $this->_mailHelper->contratarduoAdmin($sendMail);
                $sendMail['to'] = $dataForm['correo'];
                $this->_mailHelper->contratarduo($sendMail);
                $this->_Services->clienteNatural($dataForm);
                $this->messageEmail('contratar-duo');
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            $this->view->ListarDepartamentos = $this->_Services->ListarDepartamentos();
            $this->view->ListarProvincias = $this->_Services->ListarProvincias(15);
            $this->view->ListarDistritos = $this->_Services->ListarDistritos(15, 1);
        }
    }

    public function contratarNegociosAction() {
        if ($this->_request->isPost()) {
            $obj = new Application_Entity_RunSql('Cliente');
            $dataForm = $this->_request->getPost();
            try {
                $dataForm['codigo'] = $this->consultaDNI($dataForm['documento']);
                $Ubigeo = $this->_Services->ListarUbigeo($dataForm['departamento'], $dataForm['provincia'], $dataForm['distrito']);
                $dataForm['fecha'] = date('Y-m-d H:i:s');
                $dataForm['tipo'] = 'contrato';
                $dataForm['servicio'] = 'negocios';
                $dataForm['estado'] = 'pendiente';
                $obj->save = $dataForm;
                $sendMail = array(
                    'documento' => $dataForm['documento'],
                    'nombre' => $dataForm['nombre'],
                    'apellido' => $dataForm['apellido'],
                    'direccion' => $dataForm['direccion'],
                    'distrito' => $Ubigeo,
                    'fijo' => $dataForm['fijo'],
                    'celular' => $dataForm['celular'],
                    'correo' => $dataForm['correo'],
                    'tipo' => $dataForm['tipo'],
                );
                $this->_mailHelper->contratarnegociosAdmin($sendMail);
                $sendMail['to'] = $dataForm['correo'];
                $this->_mailHelper->contratarnegocios($sendMail);
                $this->_Services->clienteNatural($dataForm);
                $this->messageEmail('contratar-negocios');
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            $BuscarPlanPorCodigo = $this->_Services->BuscarPlanPorCodigo(4);
            $this->view->DetallePlan = $BuscarPlanPorCodigo;
            $this->view->ListarDepartamentos = $this->_Services->ListarDepartamentos();
            $this->view->ListarProvincias = $this->_Services->ListarProvincias(15);
            $this->view->ListarDistritos = $this->_Services->ListarDistritos(15, 1);
        }
    }


    public function reclamacionesAction() {
        if ($this->_request->isPost()) {
            $obj = new Application_Entity_RunSql('Soporte');
            $dataForm = $this->_request->getPost();
            try {
                $dataForm['Date'] = date('Y-m-d H:i:s');
                $dataForm['Estado'] = 'pendiente';
                $obj->save = $dataForm;
                $sendMail = array(
                    'Email' => $dataForm['Email'],
                    'Name' => $dataForm['Name'],
                    'Phone' => $dataForm['Phone'],
                    'Subject' => $dataForm['Subject'],
                );
                $this->_mailHelper->reclamoAdmin($sendMail);
                $sendMail['to'] = $dataForm['Email'];
                $this->_mailHelper->reclamo($sendMail);
                $this->_flashMessage->success('Gracias, Muy pronto nos comunicaremos contigo.');
                $this->_redirect('reclamaciones');
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            
        }
    }

  

    public function pagosAction() {
        
    }

    public function medidorAction() {
        
    }

    public function planesAction() {
        
    }

    public function legalAction() {
        
    }



}
