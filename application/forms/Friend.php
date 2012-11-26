<?php

class Application_Form_Friend extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName('friend');

        $friendId = new Zend_Form_Element_Hidden('friend_id');
        $friendId->setAttrib('id', 'friend_id')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setRequired(false);

        $send = new Zend_Form_Element_Submit('sendbutton');
        $send ->setLabel('Add to my Account')
            ->setAttrib('id', 'sendbutton')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($friendId, $send));
    }
}

?>