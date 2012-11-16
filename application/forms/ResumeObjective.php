<?php

class Application_Form_ResumeObjective extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $objective = new Zend_Form_Element_Textarea('objective');
        $objective ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('id', 'objective')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '7');

        $submit = new Zend_Form_Element_Submit('saveObjective');
        $submit ->setLabel('Save')
            ->setAttrib('id', 'saveObjective')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);



        $this->addElements(array($objective, $submit));

    }
}

?>