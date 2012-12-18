<?php

class Application_Form_ResumeSkills extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $identity =  Zend_Auth::getInstance()->getStorage()->read();
        $this->setName('skills');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $skill = new Zend_Form_Element_Textarea('skill');
        $skill ->setAttrib('id', 'skill')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '7');

        $delete = new Zend_Form_Element_Button('deleteSkills');
        $delete ->setLabel('Delete')
            ->setAttrib('id', 'deleteSkills')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $add = new Zend_Form_Element_Button('addSkills');
        $add ->setLabel('Add')
            ->setAttrib('id', 'addSkills')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);




        $submit = new Zend_Form_Element_Submit('saveSkills');
        $submit ->setLabel('Save')
            ->setAttrib('id', 'saveSkills')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);




        $this->addElements(array($skill, $delete, $add,  $submit));

    }
}

?>