<?php

class Application_Form_Subscriptions extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $dateFrom = new Zend_Form_Element_Text('date_from');
        $dateFrom ->setAttrib('id', 'date_from')
            ->setAttrib('class', 't-165')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $period = new Zend_Form_Element_Text('period');
        $period->setAttrib('placeholder', 'Period')
            ->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress())
            ->setAttrib('id', 'period')
            ->setErrorMessages(array('Please choose period in month'))
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array(
                '0', 'Choose Period',
                '1'   => '1',
                '2'   => '2',
                '3'   => '3',
                '4'   => '4',
                '5'   => '5',
                '6'   => '6',
                '7'   => '7',
                '8'   => '8',
                '9'   => '9',
                '10'   => '10',
                '11'   => '11',
                '12'   => '12',
            ));

        $submit = new Zend_Form_Element_Submit('saveemail');
        $submit->setAttrib('id', 'saveemail')
            ->setLabel('Save')
            ->setAttrib('class', 'button')
            ->setDecorators($this->basicDecorators);


        $this->addElements(array($dateFrom, $period, $submit));

    }


}