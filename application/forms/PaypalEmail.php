<?php

class Application_Form_PaypalEmail extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $paypalEmail = new Zend_Form_Element_Text('paypal_email');
        $paypalEmail
            //->setAttrib('placeholder', 'PayPal Email')
            ->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress())
            ->setAttrib('id', 'paypal_email')
            ->setErrorMessages(array('insert your PayPal email'))
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);


        $submit = new Zend_Form_Element_Submit('saveemail');
        $submit->setAttrib('id', 'saveemail')
               ->setLabel('Save')
            ->setAttrib('class', 'button-2 save')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($paypalEmail, $submit));

    }


}