<?php

class Admin_IndexController extends Core_Controller_ActionAdmin {

    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('admin/layout-login');
    }

    public function indexAction() {
        // Finally, run the server
        // Finally, run the server
    }

    public function noauthAction() {
        echo 'no tienes acceso para ver esta pagina';
        exit;
    }

    public function errorAction() {
        echo 'no tienes acceso para ver esta pagina';
        exit;
    }

    public function loginAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $user = $this->_getParam('txtUsername', "");
        $pass = $this->_getParam('txtPassword', "");
        $pass = md5($pass);
        if ($this->_request->isPost() && $user != "" && $pass != "") {
            $login = $this->auth($user, $pass);
            if ($login) {
                $this->_redirect('/admin/dashboard');
            } else {
                $this->_redirect('/admin');
            }
        } else {
            $this->_redirect('/admin');
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/admin');
    }

    public function editAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->setLayout('layout-admin');
        // $sendOk=false;
        $id = $this->_getParam('id', 0);
        $form = new Admin_Form_Users();
        $objType = new Admin_Model_Users();
        $QuizType = $objType->getUserId($id);

        if (!empty($id)) {
            $obj = new Application_Entity_RunSql('User');
            $obj->getone = $id;
            $dataObj = $obj->getone;
            $form->populate($dataObj);
        }
        if ($this->_request->isPost()) {
            $obj = new Application_Entity_RunSql('User');
            $dataF = $this->_request->getPost();
            $dataF['lastlogin'] = date('Y-m-d H:i:s');
            $obj->edit = $dataF;
            $this->_flashMessenger->success("Datos Actualizados Correctamente.");

            $this->_redirect("/admin/index/edit/id/$id");
        }
        $this->view->QuizType = $QuizType;
        $this->view->titulo = "Editar Mis Datos";
        $this->view->submit = "Guardar Usuario";
        $this->view->action = "/admin/index/edit/id/$id";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formUsers.phtml'))));
        echo $form;
    }

    public function updatePassAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->setLayout('layout-admin');
        $form = new Admin_Form_ChangePassword();
        $mUser = new Admin_Model_Users();
        if ($this->_request->isPost()) {
            $params = $this->getAllParams();
            if ($form->isValid($params)) {
                $clapass = md5($params['password']);
                if ($clapass == $this->_identity->password) {
                    if ($params['confirmone'] == $params['confirmtwo']) {
                        $pass = md5($params['confirmone']);
                        $mUser->updateUsersPass($pass, $this->_identity->iduser);
                        $this->_flashMessenger->success("Constraseña Cambiada Correctamente.");
                        $this->_redirect('/admin/index/update-pass');
                    } else {
                        $msg = "Las Nuevas Contraseñas no Coinciden.";
                        $this->_flashMessenger->warning($msg, 'TEMP');
                    }
                } else {
                    $msg = "Contraseña Actual Incorrecta.";
                    $this->_flashMessenger->warning($msg, 'TEMP');
                }
            } else {
                $errorMsgs = Core_FormErrorMessage::getErrors($form);
                $this->_flashMessenger->error($errorMsgs);
            }
        }
        $this->view->titulo = "Editar Mi Contraseña";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formPass.phtml'))));
        echo $form;
    }

}
