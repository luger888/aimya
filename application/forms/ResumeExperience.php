<?php

class Application_Form_ResumeExperience extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $identity =  Zend_Auth::getInstance()->getStorage()->read();
        $this->setName('experience');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $experience = new Zend_Form_Element_Textarea('experience');
        $experience ->setRequired(true)
            ->setAttrib('id', 'experience')

            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '7');

        $delete = new Zend_Form_Element_Button('deleteExperience');
        $delete ->setLabel('Delete')
            ->setAttrib('id', 'deleteExperience')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $add = new Zend_Form_Element_Button('addExperience');
        $add ->setLabel('Add')
            ->setAttrib('id', 'addExperience')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

//        $file = new Zend_Form_Element_File('file');
//        $file ->setRequired(false)
//            ->setLabel('Upload File')
//            ->setAttrib('id', 'file')
//            ->addValidator('Size', false, 1024000)
//            ->addValidator('Extension', false, 'jpg,png,gif,jpeg')
//            ->setDestination('./img/uploads/'.$identity->id.'/experience/');

        $submit = new Zend_Form_Element_Submit('saveExperience');
        $submit ->setLabel('Save')
            ->setAttrib('id', 'saveExperience')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);



        $this->addElements(array($experience, $delete, $add, $submit));

    }
}

?>