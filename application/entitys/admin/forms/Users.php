<?php

class Admin_Form_Users extends Core_Form_Form
{
    public function init() {
        $obj = new Application_Model_DbTable_User();
        $primaryKey = $obj->getPrimaryKey();
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idAdmin',$primaryKey);
        $this->setAction('/admin/index/edit');
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
        $e = new Zend_Form_Element_Text('login');        
        $this->addElement($e);     
        $e = new Zend_Form_Element_Text('name');        
        $this->addElement($e); 
        $e = new Zend_Form_Element_Text('apepat');        
        $this->addElement($e);
        $e = new Zend_Form_Element_Text('apemat');        
        $this->addElement($e);  
        $e = new Zend_Form_Element_Submit('Guardar');        
        $this->addElement($e);  
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    
    public function populate(array $values) {
        if(isset($values['state'])) 
            $values['state'] = $values['state'] == '1' ? 1 : 0;
        
        parent::populate($values);
    }
}