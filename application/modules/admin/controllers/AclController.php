<?php

class Admin_AclController extends Core_Controller_Action {

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
        $obj = new Application_Entity_DataTable('Acl', $iDisplayLength, $sEcho, true);
        $obj->setIconAction($this->action());
        $query = "";
        $query.=!empty($sSearch) ? " AND urlacl like '%" . $sSearch . "%' " : " ";
        $obj->setSearch($query);
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
                ->appendBody(json_encode($obj->getQuery($iDisplayStart, $iDisplayLength)));
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id', 0);
        $form = new Admin_Form_Acl();
        if (!empty($id)) {
            $obj = new Application_Entity_RunSql('Acl');
            $obj->getone = $id;
            $dataObj = $obj->getone;
            $form->populate($dataObj);
        }
        $this->view->id = $id;
        $this->view->titulo = "Editar Acceso";
        $this->view->submit = "Guardar cambios";
        $this->view->action = "/admin/acl/new";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formAcl.phtml'))));
        echo $form;
    }

    public function newAction() {
        $form = new Admin_Form_Acl();
        $obj = new Application_Entity_RunSql('Acl');
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                if ($form->isValid($dataForm)) {
                    $msj = array();
                    if (empty($dataForm['idacl'])) {
                        $dataForm['creatingDate'] = date('Y-m-d H:i:s');
                        $dataForm['lastUpdate'] = date('Y-m-d H:i:s');
                        $obj->save = $dataForm;
                    } else {
                        $dataForm['lastUpdate'] = date('Y-m-d H:i:s');
                        $obj->edit = $dataForm;
                    }
                    $this->_redirect('/admin/acl');
                } else {
                    $this->_flashMessenger->success('error en los campos');
                }
                $this->_redirect('/admin/acl/new');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $this->view->titulo = "Nuevo Acl";
            $this->view->submit = "Guardar";
            $this->view->action = "/admin/acl/new";
            $form->addDecoratorCustom('forms/_formAcl.phtml');
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
                $obj = new Application_Entity_RunSql('Acl');
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
        $action = "<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/admin/acl/edit?id=__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/admin/acl/delete?id=__ID__\">Eliminar</a>";
        return $action;
    }

}
