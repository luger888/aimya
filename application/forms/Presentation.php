<?php

class Application_Form_Presentation extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        //$identity =  Zend_Auth::getInstance()->getStorage()->read();
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $presentation = new Zend_Form_Element_File('Filedata');
        $presentation ->setLabel('Upload Image')
             ->setAttrib('id', 'Filedata')
             ->addValidator('Size', false, 25024000)
             ->addValidator('Extension', false, 'ppt,pdf,pptx');
             //->setDestination('./flash/presentation/'.$identity->id.'/1/');



        $this->addElements(array($presentation));

    }
}

?>