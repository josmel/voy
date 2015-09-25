<?php

class Admin_Form_Role extends Core_Form_Form {

    protected $_idrol = '';

    public function __construct($id = null, $options = null) {
        $this->_idrol = $id;
        parent::__construct($options);
    }

    public function init() {
        $roleDt = new Application_Model_DbTable_Role();
        $primaryKey = $roleDt->getPrimaryKey();
        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idrol', $primaryKey);
        $this->setAction('/admin/role/edit');
        $objType2 = new Admin_Model_Acl();
        $objfea = new Admin_Model_AclRole();
        $e = new Zend_Form_Element_Hidden($primaryKey);
        $this->addElement($e);
        $e = new Zend_Form_Element_MultiCheckbox('idacl');
        $e->setMultiOptions($objType2->getGreatAll());
        if ($this->_idrol !== null) {
            $ma = $objfea->getRoleAcl($this->_idrol);
            $idsgreat = array();
            foreach ($ma as $resulta) {
                $idsgreat[] = $resulta['idacl'];
            }
            $e->setValue($idsgreat);
            $role = new Admin_Model_Role();
            $b = $role->getRoleId($this->_idrol);
        }
        $this->addElement($e);
        $e = new Zend_Form_Element_Text('name');
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Nombre');
        $this->addElement($e);

        $e = new Zend_Form_Element_Checkbox('state');
        $e->setValue(true);
        $this->addElement($e);
        foreach ($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('HtmlTag');
        }
    }

    public function populate(array $values) {
        if (isset($values['state']))
            $values['state'] = $values['state'] == '1' ? 1 : 0;
        parent::populate($values);
    }

}
