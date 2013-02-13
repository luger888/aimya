<?php

class Application_Form_Friend extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName('friend');

        //$currentUrl = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();

        $friendId = new Zend_Form_Element_Hidden('friend_id');
        $friendId->setAttrib('id', 'friend_id')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setRequired(true);

        $url = new Zend_Form_Element_Hidden('url');
        $url->setAttrib('id', 'url')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setRequired(true);

        $requestComment = new Zend_Form_Element_Textarea('request_comment');
        $requestComment->setAttrib('id', 'request_comment')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '5')
            ->setAttrib('cols', '55')
            ->setRequired(true);

        $send = new Zend_Form_Element_Submit('sendbutton');
        $send ->setLabel('send')
            ->setAttrib('id', 'sendbutton')
            ->setAttrib('class', 'button')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($friendId, $url, $requestComment, $send));
    }
}

?>