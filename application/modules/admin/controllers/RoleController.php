<?php

class Admin_RoleController extends Core_Controller_ActionAdmin {

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
        $obj = new Application_Entity_DataTable('Role', $iDisplayLength, $sEcho, true);
        $obj->setIconAction($this->action());
        $query = "";
        $query.=!empty($sSearch) ? " AND name like '%" . $sSearch . "%' " : " ";
        $obj->setSearch($query);
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
                ->appendBody(json_encode($obj->getQuery($iDisplayStart, $iDisplayLength)));
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id', 0);
        $form = new Admin_Form_Role($id);
        if (!empty($id)) {
            $obj = new Application_Entity_RunSql('Role');
            $obj->getone = $id;
            $dataObj = $obj->getone;
            $form->populate($dataObj);
        }
        $this->view->titulo = "Editar Rol";
        $this->view->submit = "Guardar cambios";
        $this->view->action = "/admin/role/new";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formRole.phtml'))));
        echo $form;
    }

    public function newAction() {
        $form = new Admin_Form_Role();
        $obj = new Application_Entity_RunSql('Role');
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                $aclRole = new Admin_Model_AclRole();
                if (empty($dataForm['idrol'])) {
                    $dataForm['creatingDate'] = date('Y-m-d H:i:s');
                    $dataForm['lastUpdate'] = date('Y-m-d H:i:s');
                    $obj->save = $dataForm;
                    $aclRole->insertAclRole($obj->save, $dataForm['idacl']);
                } else {
                    $aclRole->deletRole($dataForm['idrol']);
                    $aclRole->insertAclRole($dataForm['idrol'], $dataForm['idacl']);
                    $dataForm['lastUpdate'] = date('Y-m-d H:i:s');
                    $obj->edit = $dataForm;
                }
                $this->_redirect('/admin/role');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $this->view->titulo = "Nuevo Rol";
            $this->view->submit = "Guardar";
            $this->view->action = "/admin/role/new";
            $form->addDecoratorCustom('forms/_formRole.phtml');
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
                $aclRole = new Admin_Model_Users();
                $da = $aclRole->getUserRoles($id);
                if (count($da) == 0) {
                    $aclRole = new Admin_Model_AclRole();
                    $aclRole->deletRole($id);
                    $obj = new Application_Entity_RunSql('Role');
                    $obj->erase = $id;
                    $rpta['msj'] = 'ok';
                } else { 
//                    $msg = "El rol esta siendo utilizado por usuarios activos";
//                    $this->_flashMessenger->success($msg);
//                    $this->_redirect('/admin/role');
                $rpta['msj'] = 'El rol esta siendo utilizado por usuarios activos';
                }
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
        $action = "<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/admin/role/edit?id=__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/admin/role/delete?id=__ID__\">Eliminar</a>";
        return $action;
    }

}
