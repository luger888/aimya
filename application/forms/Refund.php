<?php

class Application_Form_Refund extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName("refund_form");

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress())
            ->setAttrib('placeholder', 'E-mail')
            ->setAttrib('class', 'clearInput regTextInput email')
            ->setErrorMessages(array('Insert your email'))
            ->setLabel('email address:');

        $submit = new Zend_Form_Element_Submit('send');
        $submit->setAttrib('id', 'send')
            ->setLabel('Send')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($email, $submit));

    }


}