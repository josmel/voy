<?php

class Core_Acl extends Zend_Acl {

    const GUEST = 'guest';

    public function __construct() {
        $this->addRole(new Zend_Acl_Role(self::GUEST));
        $this->add(new Zend_Acl_Resource('admin::error::error-privilegio'));
        $this->add(new Zend_Acl_Resource('admin::index::error404'));
        $this->add(new Zend_Acl_Resource('admin::index::index'));
        $this->add(new Zend_Acl_Resource('admin::index::login'));
        $this->add(new Zend_Acl_Resource('admin::index::logout'));
        $this->add(new Zend_Acl_Resource('admin::dashboard::index'));
        $this->add(new Zend_Acl_Resource('admin::index::*'));
        $this->add(new Zend_Acl_Resource('voy::*'));
        $this->allow(self::GUEST, 'admin::error::error-privilegio');
        $this->allow(self::GUEST, 'admin::index::error404');
        $this->allow(self::GUEST, 'admin::index::index');
        $this->allow(self::GUEST, 'admin::index::login');
        $this->allow(self::GUEST, 'admin::index::logout');
        $this->allow(self::GUEST, 'admin::dashboard::index');
        $this->allow(self::GUEST, 'admin::index::*');
        $this->allow(self::GUEST, 'voy::*');
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
                $this->addRole(new Zend_Acl_Role($item['name']), self::GUEST);
                $aclsRole = $modelAcl->getAclByRole($item['idrol']);
                foreach ($aclsRole as $permission) {
                    $this->allow($item['name'], $permission);
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
                exit;
            }
        }
    }

}
