<?php

class Application_Form_ResumeEducation extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $identity =  Zend_Auth::getInstance()->getStorage()->read();
        $this->setName('education');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $education = new Zend_Form_Element_Textarea('education');
        $education ->setAttrib('id', 'education')
            ->addFilters($this->basicFilters)
            ->setAttrib('maxlength', '1000')
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '7');

        $delete = new Zend_Form_Element_Button('deleteEducation');
        $delete ->setLabel('Delete')
            ->setAttrib('id', 'deleteEducation')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $add = new Zend_Form_Element_Button('addEducation');
        $add ->setLabel('Add')
            ->setAttrib('id', 'addEducation')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

//        $certificate = new Zend_Form_Element_File('certificate');
//        $certificate ->setLabel('Upload Certificate')
//            ->setAttrib('id', 'certificate')
//            ->addValidator('Size', false, 1024000)
//            ->addValidator('Extension', false, 'jpg,png,gif,jpeg')
//            ->setDestination('./img/uploads/'.$identity->id.'/education/');

        $submit = new Zend_Form_Element_Submit('saveEducation');
        $submit ->setLabel('Save')
            ->setAttrib('id', 'saveEducation')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);




        $this->addElements(array($education, $delete, $add,  $submit));

    }
}

?>