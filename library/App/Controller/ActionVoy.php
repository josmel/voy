<?php

class App_Controller_ActionVoy extends Zend_Controller_Action {

    public function init() {
        $this->_config = Zend_Registry::get('config');
        $this->_helper->layout->setLayout('voy/layout-voy');
        $this->_flashMessage = new Core_Controller_Action_Helper_FlashMessengerCustom();
        $this->_mailHelper = $this->_helper->getHelper('Mail');
  
     
    }
    
     public function messageEmail($ruta) {
        $this->_flashMessage->success('¡Gracias!'
                . '<br>'
                . 'Pronto nos comunicaremos contigo para coordinar la instalaci&oacute;n de tu servicio');
        $this->_redirect('/' . $ruta);
    }
}