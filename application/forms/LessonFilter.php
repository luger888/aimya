<?php

class Application_Form_LessonFilter extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $details = new Zend_Form_Element_Select('details');
        $details->setAttrib('id', 'details')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array('0' => 'All',
            '1' => 'Notes',
            '2' => 'Feedback',
            '3' => 'Video REC'
        ));


        $fromPeriod = new Zend_Form_Element_Text('fromPeriod');
        $fromPeriod ->setAttrib('id', 'fromPeriod')
            ->setAttrib('class', 'timePickerFilter')
            ->setRequired(true)
            ->addValidator('NotEmpty')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $toPeriod = new Zend_Form_Element_Text('toPeriod');
        $toPeriod ->setAttrib('id', 'toPeriod')
            ->setAttrib('class', 'timePickerFilter')
            ->setRequired(true)
            ->addValidator('NotEmpty')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);


        $submit = new Zend_Form_Element_Submit('filter');
        $submit->setAttrib('class', 'button-2 display floatRight')
        ->setLabel('display')
            ->setDecorators($this->basicDecorators);
        $this->addElements(array($details, $fromPeriod, $toPeriod, $submit));
    }
}

?>