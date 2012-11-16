<?php

class Application_Form_Message extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName('message');

        /*$recipientId = new Zend_Form_Element_Hidden('recipientid');
        $recipientId->setAttrib('id', 'recipientid')
            //->addValidator('NotEmpty')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);*/

        $email = new Zend_Form_Element_Text('username');
        $email->addValidator('NotEmpty')
            ->setAttrib('id', 'username')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $subject = new Zend_Form_Element_Text('subject');
        $subject->setAttrib('id', 'subject')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $content = new Zend_Form_Element_Textarea('content');
        $content->addValidator('NotEmpty')
            ->setAttrib('id', 'content')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setAttrib('rows', '7');


        $send = new Zend_Form_Element_Submit('sendbutton');
        $send ->setLabel('Send')
            ->setAttrib('id', 'sendbutton')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($email, $subject, $content, $send));

    }
}

?>