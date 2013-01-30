<?php

class Application_Form_Refund extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName("refund_form");

        $subscriptionId = new Zend_Form_Element_Hidden('subscription_id');
        $subscriptionId->setAttrib('id', 'subscription_id')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setRequired(true);

        /*$url = new Zend_Form_Element_Hidden('url');
        $url->setAttrib('id', 'url')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setRequired(true);*/

        $period = new Zend_Form_Element_Select('period');
        $period->setAttrib('placeholder', 'Period')
            ->setRequired(true)
            ->setAttrib('id', 'period')
            ->setErrorMessages(array('Please choose period in month'))
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array(
            '0'   => 'Choose Period',
            '1'   => '1 month',
            '2'   => '2 months',
            '3'   => '3 months',
            '4'   => '4 months',
            '5'   => '5 months',
            '6'   => '6 months',
            '7'   => '7 months',
            '8'   => '8 months',
            '9'   => '9 months',
            '10'   => '10 months',
            '11'   => '11 months',
            '12'   => '12 months',
        ));

        $amount = new Zend_Form_Element_Text('amount');
        $amount->setAttrib('id', 'amount')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addValidator('Digits')
            ->setRequired(true);

        $requestComment = new Zend_Form_Element_Textarea('request_comment');
        $requestComment->setAttrib('id', 'request_comment')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '3')
            ->setAttrib('cols', '55')
            ->setRequired(true);

        $send = new Zend_Form_Element_Submit('sendbutton');
        $send ->setLabel('ok')
            ->setAttrib('id', 'sendbutton')
            ->setAttrib('class', 'button')
            ->setAttrib('onclick', 'approveRefund($("#subscription_id").val()); return false;')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($subscriptionId, $period, $amount, $requestComment, $send));

    }


}