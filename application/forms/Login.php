<?php

class Application_Form_Login extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $username = new Zend_Form_Element_Text('username');
        $username#->setAttrib('placeholder', 'username or email')
            ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('id', 'username-login')
            ->setErrorMessages(array('Insert your login'))
            ->setAttrib('class', 'customInput logTextInput login clearInput')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
            ->addValidator('NotEmpty')
            #->setAttrib('placeholder', 'password')
            ->setErrorMessages(array('Insert your password'))
            ->setAttrib('class', 'customInput logTextInput clearInput')
            ->setAttrib('id', 'password-login')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $remember = new Zend_Form_Element_Checkbox('remember');
        $remember->setDecorators($this->basicDecorators);


        $submit = new Zend_Form_Element_Submit('login');
        $submit->setAttrib('id', 'login')
               ->setLabel('LOGIN')
            ->setAttrib('class', 'btnLogin')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($username, $password, $submit, $remember));

    }


}