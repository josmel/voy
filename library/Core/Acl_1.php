<?php

class Core_Acl extends Zend_Acl {

    const GUEST = 'guest';

    public function __construct() {

        $this->addRole(new Zend_Acl_Role(self::GUEST));

//        $this->addRole(new Zend_Acl_Role('invitado'), self::GUEST);
//        $this->addRole(new Zend_Acl_Role('admin'));

        $this->add(new Zend_Acl_Resource('admin::error::error'));
        $this->add(new Zend_Acl_Resource('admin::index::error404'));
        $this->add(new Zend_Acl_Resource('admin::index::index'));
        $this->add(new Zend_Acl_Resource('admin::index::login'));
        $this->add(new Zend_Acl_Resource('admin::index::logout'));

        $this->add(new Zend_Acl_Resource('voy::*'));

        $this->allow(self::GUEST, 'admin::error::error');
        $this->allow(self::GUEST, 'admin::index::error404');
        $this->allow(self::GUEST, 'admin::index::index');
        $this->allow(self::GUEST, 'admin::index::login');
        $this->allow(self::GUEST, 'admin::index::logout');
        $this->allow(self::GUEST, 'voy::*');
        
//        $this->allow('admin');
        
        $modelAcl = new Application_Model_Acl();
        $listAcl = $modelAcl->getListResources();
        foreach ($listAcl as $resource) {
            try {
                if (!$this->has($resource))
                    $this->add(new Zend_Acl_Resource($resource));
            } catch (Exception $ex) {
                
            }
        }
        $modelRole = new Application_Model_Role();
        $roles = $modelRole->getAllRoles();


        foreach ($roles as $item) {
            try {
                $this->addRole(new Zend_Acl_Role($item['desrol']), self::GUEST);
                $aclsRole = $modelAcl->getAclByRole($item['idrol']);
                foreach ($aclsRole as $permission) {
                    $this->allow($item['desrol'], $permission);
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
                exit;
            }
        }
        //  $this->add(new Zend_Acl_Resource('admin::tipo-antecedentes'));
        //PERMISOS
    }

}

//        $this->addRole(new Zend_Acl_Role(self::INVITADO));
//        // Add a new role called "guest"
//        $this->addRole(new Zend_Acl_Role('guest'));
//        // Add a role called user, which inherits from guest
//        $this->addRole(new Zend_Acl_Role('user'), 'guest');
//        // Add a role called admin, which inherits from user
//        $this->addRole(new Zend_Acl_Role('admin'), 'user');
//
//        // Add some resources in the form controller::action
//        $this->add(new Zend_Acl_Resource('admin::index::error'));
//        $this->add(new Zend_Acl_Resource('admin::index::login'));
//        $this->add(new Zend_Acl_Resource('admin::index::logout'));
//        $this->add(new Zend_Acl_Resource('admin::index::index'));
//        $this->add(new Zend_Acl_Resource('admin::index::basico'));
//        ;
//        $this->allow('guest', 'admin::index::logout');
//        // Allow guests to see the error, login and index pages
//        $this->allow('guest', 'admin::index::error');
//        $this->allow('guest', 'admin::index::login');
//        $this->allow('guest', 'admin::index::index');
//
//        // Allow users to access logout and the index action from the user controller
//        //var_dump($f);exit;
//        $this->allow('user', 'admin::index::index');
//
//        // Allow admin to access admin controller, index action
//        $this->allow('admin', 'admin::index::index');
//
//
//        $modelAcl = new Application_Model_Acl();
//        $listAcl = $modelAcl->getListResources();
//        foreach ($listAcl as $resource) {
//            try {
//                if (!$this->has($resource))
//                    $this->add(new Zend_Acl_Resource($resource));
//            } catch (Exception $ex) {
//                
//            }
//        }
//        $modelRole = new Application_Model_Role();
//        $roles = $modelRole->getAllRoles();
//        
//        foreach ($roles as $item) {
//            try {
//                $this->addRole(new Zend_Acl_Role($item['desrol']), self::INVITADO);
//                $aclsRole = $modelAcl->getAclByRole($item['idrol']);
//                foreach ($aclsRole as $permission)
//                    $this->allow($item['desrol'], $permission);
//            } catch (Exception $ex) {
//                echo $ex->getMessage();exit;
//            }
//        }
//
//
//
//
//
//
//
//        // You will add here roles, resources and authorization specific to your application, the above are some examples
//    }
//
//}
