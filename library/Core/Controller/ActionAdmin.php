<?php

class Core_Controller_ActionAdmin extends Core_Controller_Action {

    protected $_identity;

    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('layout-admin');
    }

    public function preDispatch() {
        parent::preDispatch();
        $this->permisos();
        $this->_identity = Zend_Auth::getInstance()->getIdentity();

        $this->view->identity = $this->_identity;
    }

    function permisos() {
        $auth = Zend_Auth::getInstance();
        $controller = $this->_request->getControllerName();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
            $modelAcl = new Application_Model_Acl();
            $aclsRole = $modelAcl->getAclByRole($user->idrol);
            foreach ($aclsRole as $permission) {
                $actions[] = explode('::', $permission);
            }
            $this->view->menu = $this->getMenuAdmin($actions);
        } else {
            if ($controller != 'index') {
                $this->_redirect('/');
            }
        }
    }

    function getMenuAdmin($actions) {
        $menu['dashboard'] = array('class' => 'icad-dashb', 'url' => '/admin/dashboard', 'title' => 'DASHBOARD');
        foreach ($actions as $role => $parents) {
            $menu[$parents[1]] = array('class' => 'icad-prom', 'url' => '/' . $parents[0] . '/' . $parents[1], 'title' => $parents[1]);
        }
        return $menu;
    }

    public function auth($usuario, $password, $url = null) {
        $dbAdapter = Zend_Registry::get('multidb');
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
      //  $hash = password_hash($password, PASSWORD_DEFAULT);
        $authAdapter
                ->setTableName('tusers')
                ->setIdentityColumn('login')
                ->setCredentialColumn('password')
                ->setIdentity($usuario)
                ->setCredential($password);
        try {
            $select = $authAdapter->getDbSelect();
            $select->where('state = 1');
            $result = Zend_Auth::getInstance()->authenticate($authAdapter);
            if ($result->isValid()) {
                $storage = Zend_Auth::getInstance()->getStorage();
                $bddResultRow = $authAdapter->getResultRowObject();
                $storage->write($bddResultRow);
                $msj = 'Bienvenido Usuario ' . $result->getIdentity();
                $this->_identity = Zend_Auth::getInstance()->getIdentity();
                if (isset($mysession->destination_url)) {
                    $url = $mysession->destination_url;
                    unset($mysession->destination_url);
                    $this->_redirect($url);
                }
                if (!empty($url)) {
                    $this->_redirect($url);
                }
                $return = true;
            } else {
                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        $msj = 'El usuario no existe';
                        break;
                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        $msj = 'Password incorrecto';
                        break;
                    case Zend_Auth_Result:: FAILURE_IDENTITY_AMBIGUOUS:
                        $msj = 'Identidad Ambigua';
                        break;
                    case Zend_Auth_Result::FAILURE_UNCATEGORIZED:
                        $msj = 'Credencial Fracasada';
                        break;
                    default:
                        $msj = 'Datos incorrectos';
                        break;
                }
                $this->_flashMessenger->warning($msj);
                $return = false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
        return $return;
    }

}
