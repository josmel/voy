<?php

class Voy_NegociosController extends App_Controller_ActionVoy {

    protected $_sessionPlan;

    public function init() {
        parent::init();
        $this->_Services = new App_Controller_Services();
        $this->view->fecha = $this->_Services->obtenerUltimoDiaMesActual();
        $this->_sessionPlan = new Zend_Session_Namespace('sessionPlan');
    }

        public function negociosAction() {
        $ListarPlanesNegocios = $this->_Services->ListarPlanes(2);
        $TipoPlan = $this->_Services->BuscarTipoPlan(2);
        $this->view->TipoPlan = $TipoPlan['legal'];
        $this->view->ListarPlanesNegocios = $ListarPlanesNegocios;
    }

}
