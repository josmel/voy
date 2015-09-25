<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of acll
 *
 * @author jyupanqui
 */
class Core_Controller_Plugin_ACL extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $auth = Zend_Auth::getInstance();
        $acl = new Core_Acl();
        $mysession = new Zend_Session_Namespace('mysession');
        if ($auth->hasIdentity()) { 
            $user = $auth->getIdentity();
            $modelRole = new Application_Model_Role();
            $roles = $modelRole->getRolByUser($user->iduser);
                if (!$acl->isAllowed($roles, $request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName())) {
                $mysession->destination_url = $request->getPathInfo();
                return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoUrl('admin/error/error');
            }
        } else {
            if (!$acl->isAllowed(Core_Acl::GUEST, $request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName())) {
                $mysession->destination_url = $request->getPathInfo();
                return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoUrl('/admin/');
            }
        }
    }

}
