<?php

class Admin_Form_Acl extends Core_Form_Form {

   
    public function init() {
        $roleDt = new Application_Model_DbTable_Acl();
        $primaryKey = $roleDt->getPrimaryKey();
        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idacl', $primaryKey);
        $this->setAction('/admin/acl/edit');
        $e = new Zend_Form_Element_Hidden($primaryKey);
        $this->addElement($e);
        $e = new Zend_Form_Element_Text('urlacl');
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Ruta de Acceso');
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
