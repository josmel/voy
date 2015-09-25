<?php

class Admin_Form_UserAdmin extends Core_Form_Form {


    public function init() {

        $obj = new Application_Model_DbTable_User();
        $primaryKey = $obj->getPrimaryKey();
        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('iduser', $primaryKey);
        $this->setAction('/admin/usuarios/edit');
        $e = new Zend_Form_Element_Hidden($primaryKey);
        $this->addElement($e);

        $objType = new Admin_Model_Role();
        $e = new Zend_Form_Element_Select('idrol');
        $e->setMultiOptions($objType->getRoleAll());
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('email');
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Correo');
        $this->addElement($e);
        $e = new Zend_Form_Element_Text('login');
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'usuario');
        $this->addElement($e);
        $e = new Zend_Form_Element_Text('name');
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'nombre');
        $this->addElement($e);
        $e = new Zend_Form_Element_Text('apepat');
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Apellido Paterno');
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('apemat');
            $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Apellido Materno');
        $this->addElement($e);
        $e = new Zend_Form_Element_Submit('Guardar');
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Checkbox('state');
        $e->setValue(true);
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Password('confirmone');  
        $e->setRequired(false);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Contraseña');
        $this->addElement($e); 
        $e = new Zend_Form_Element_Password('confirmtwo');  
        $e->setRequired(false);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Repetir Contraseña');
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
