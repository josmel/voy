<?php


class Voy_SoporteController extends App_Controller_ActionVoy {


    public function init() {
        parent::init();
        $this->_Services = new App_Controller_Services();
    }

    public function soporteAction() {
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
                    'asunto' => 'Voy - Solicitud de soporte'
                );
                $this->_mailHelper->solicitudAdmin($sendMail);
                $sendMail['to'] = $dataForm['Email'];
                $sendMail['asunto'] = 'Gracias por contactarte con nosotros';
                $this->_mailHelper->solicitud($sendMail);
                $this->_flashMessage->success('Gracias, Muy pronto nos comunicaremos contigo.');
                $this->_redirect('soporte');
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            
        }
    }

}
