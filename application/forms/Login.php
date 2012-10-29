<?php

class Application_Form_Login extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init(){

        $username = new Zend_Form_Element_Text('username');
        $username ->setAttrib('placeholder', 'username or email')
            ->setAttrib('id', 'username')
            ->setAttrib('class', 'customInput required login clearInput')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $password = new Zend_Form_Element_Password('password');
        $password   ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('placeholder', 'password')
            ->setAttrib('class', 'customInput required clearInput')
            ->setAttrib('id', 'password')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $remember = new Zend_Form_Element_Checkbox('remember');
        $remember ->setDecorators($this->basicDecorators);


        $submit = new Zend_Form_Element_Submit('login');
        $submit ->setAttrib('id', 'login')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);

        $this->addElements(array($username, $password, $submit, $remember ));

    }


}