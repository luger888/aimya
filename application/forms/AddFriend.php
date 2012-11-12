<?php

class Application_Form_AddFriend extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $recipientId = new Zend_Form_Element_Hidden('recipientid');
        $recipientId->setAttrib('id', 'recipientid')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $submit = new Zend_Form_Element_Submit('addtoaccount');
        $submit->setAttrib('id', 'addtoaccount')
            ->setLabel('Add to Account')
            ->setAttrib('class', 'btn btnStrict')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($recipientId, $submit));

    }


}