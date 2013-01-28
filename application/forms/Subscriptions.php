<?php

class Application_Form_Subscriptions extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $subscriptionDb = new Application_Model_DbTable_Subscriptions();

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

        $subscribe = new Zend_Form_Element_Submit('subscribe');
        $subscribe->setAttrib('id', 'subscribe')
            ->setLabel('Subscribe')
            ->setAttrib('class', 'button')
            ->setDecorators($this->basicDecorators);


        $unSubscribe = new Zend_Form_Element_Button('unsubscribe');
        $unSubscribe->setAttrib('id', 'unsubscribe')
            ->setLabel('Unsubscribe')
            ->setAttrib('class', 'button')
            ->setDecorators($this->basicDecorators);

        if($subscriptionDb->isRefundEnable()){
            $unSubscribe->setAttrib('onClick', 'refund();');
        }else{
            $unSubscribe->setAttrib('class', 'disable button');

        }


        $this->addElements(array($period, $subscribe, $unSubscribe));

    }


}