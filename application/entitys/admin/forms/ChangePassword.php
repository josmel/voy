<?php

class Admin_Form_ChangePassword extends Core_Form_Form {
    
    public function init() {
        $obj = new Application_Model_DbTable_User();
        $primaryKey = $obj->getPrimaryKey();
        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('codempr', $primaryKey);
        $this->setAction('/admin/index/update-pass');
        
        $e = new Zend_Form_Element_Password('password');  
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Contraseña actual');
        $this->addElement($e);   
        
        $e = new Zend_Form_Element_Password('confirmone');  
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Nueva Contraseña');
        $this->addElement($e); 
        
        $e = new Zend_Form_Element_Password('confirmtwo');  
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Repetir Nueva Contraseña');
        $this->addElement($e); 
        $e = new Zend_Form_Element_Submit('Cambiar');
        $this->addElement($e);
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    public function isValid($data) {
        $isValid = parent::isValid($data);
        
        return $isValid;
    }
        
}