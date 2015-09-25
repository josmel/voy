<?php


class Voy_OtrosController extends App_Controller_ActionVoy {


    public function init() {
        parent::init();
    }

    public function otrosAction() {
        if ($this->_request->isPost()) {
            $obj = new Application_Entity_RunSql('Cliente');
            $dataForm = $this->_request->getPost();
            try {
                $dataForm['fecha'] = date('Y-m-d H:i:s');
                $dataForm['tipo'] = 'reserva';
                $dataForm['estado'] = 'reserva';
                $obj->save = $dataForm;
                $sendMail = array(
                    'documento' => $dataForm['documento'],
                    'nombre' => $dataForm['nombre'],
                    'apellido' => $dataForm['apellido'],
                    'direccion' => $dataForm['direccion'],
                    'fijo' => $dataForm['fijo'],
                    'celular' => $dataForm['celular'],
                    'correo' => $dataForm['correo'],
                    'tipo' => $dataForm['tipo'],
                );
                $this->_mailHelper->registroAdmin($sendMail);
                $sendMail['to'] = $dataForm['correo'];
                $this->_mailHelper->registro($sendMail);
                $this->messageEmail('otros');
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            
        }
    }

}
