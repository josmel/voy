<?php

class Admin_UsuariosController extends Core_Controller_ActionAdmin {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        
    }

    public function listAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = $this->_getAllParams();
        $iDisplayStart = isset($params['iDisplayStart']) ? $params['iDisplayStart'] : null;
        $iDisplayLength = isset($params['iDisplayLength']) ? $params['iDisplayLength'] : null;
        $sEcho = isset($params['sEcho']) ? $params['sEcho'] : 1;
        $sSearch = isset($params['sSearch']) ? $params['sSearch'] : '';
        $obj = new Application_Entity_DataTableUser('User', $iDisplayLength, $sEcho, true, $sSearch);
        $obj->setIconAction($this->action());
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
                ->appendBody(json_encode($obj->getQuery($iDisplayStart, $iDisplayLength)));
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id', 0);
        $form = new Admin_Form_UserAdmin();
        if (!empty($id)) {
            $obj = new Application_Entity_RunSql('User');
            $obj->getone = $id;
            $dataObj = $obj->getone;
            $form->populate($dataObj);
        }
        $this->view->id = $id;
        $this->view->titulo = "Editar Usuario";
        $this->view->submit = "Guardar cambios";
        $this->view->action = "/admin/usuarios/new";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formUserAdmin.phtml'))));
        echo $form;
    }

    public function newAction() {
        $form = new Admin_Form_UserAdmin();
        $obj = new Application_Entity_RunSql('User');
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                if ($form->isValid($dataForm)) {
                    $msj = array();
                    if (empty($dataForm['iduser'])) {
                        if ($dataForm['confirmone'] == $dataForm['confirmtwo']) {
                            $dataForm['password'] = md5($dataForm['confirmone']);
                            $dataForm['lastlogin'] = date('Y-m-d H:i:s');
                            $obj->save = $dataForm;
                        } else {
                            $msg = "Las Contraseñas no Coinciden.";
                            $this->_flashMessenger->success($msg);
                            $this->_redirect('/admin/usuarios/new');
                        }
                    } else {
                        $dataForm['lastpasschange'] = date('Y-m-d H:i:s');
                        $obj->edit = $dataForm;
                    }
                    $this->_redirect('/admin/usuarios');
                } else {
                    $this->_flashMessenger->success('error en los campos');
                }
                $this->_redirect('/admin/usuarios/new');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $this->view->titulo = "Nuevo Usuario";
            $this->view->submit = "Guardar";
            $this->view->action = "/admin/usuarios/new";
            $form->addDecoratorCustom('forms/_formUserAdmin.phtml');
            echo $form;
        }
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getParam('id');
        $rpta = array();
        if (!empty($id)) {
            try {
                $obj = new Application_Entity_RunSql('User');
                $obj->erase = $id;
                $rpta['msj'] = 'ok';
            } catch (Exception $e) {
                $rpta['msj'] = $e->getMessage();
            }
        } else {
            $rpta['msj'] = 'faltan datos';
        }
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
                ->appendBody(json_encode($rpta));
    }

    function action() {
        $action = "<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/admin/usuarios/edit?id=__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/admin/usuarios/delete?id=__ID__\">Eliminar</a>";
        return $action;
    }

}
