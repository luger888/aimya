<?php

class Application_Form_Profile extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $this->setName('profile');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $avatar = new Zend_Form_Element_File('avatar');
        $avatar ->setAttrib('id', 'avatar')
             ->addValidator('Size', false, 1024000)
             ->addValidator('Extension', false, 'jpg,png,gif,jpeg')
             ->setDestination('./img/uploads/');


        $firstName = new Zend_Form_Element_Text('firstname');
        $firstName ->setAttrib('placeholder', 'First Name')
            ->setAttrib('class', 'required')
            ->setAttrib('id', 'firstname')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $lastName = new Zend_Form_Element_Text('lastname');
        $lastName ->setAttrib('class', 'required')
            ->setAttrib('placeholder', 'Last Name')
            ->setAttrib('id', 'lastname')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $birthday = new Zend_Form_Element_Text('birthday');
        $birthday ->setAttrib('id', 'birthday')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $language = new Zend_Form_Element_Text('language');
        $language ->setAttrib('placeholder', 'Language Spoken')
            ->setAttrib('id', 'language')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $username = new Zend_Form_Element_Text('username');
        $username ->setAttrib('id', 'username')
            ->setAttrib('placeholder', 'username')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $email = new Zend_Form_Element_Text('email');
        $email#->addValidator(new Zend_Validate_EmailAddress())
            ->setAttrib('placeholder', 'E-mail')
            ->setAttrib('placeholder', 'email address')
            ->setAttrib('class', 'clearInput required email')
            ->setDecorators($this->basicDecorators);

        $timeZone = new Zend_Form_Element_Select('timezone');
        $timeZone->setAttrib('id', 'timezone')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array('0'   => 'time zone'));

        $intro = new Zend_Form_Element_Textarea('add_info');
        $intro->addValidator('NotEmpty')
            ->setAttrib('id', 'intro')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            -> setAttrib('rows', '7');

        $submit = new Zend_Form_Element_Submit('saveProfile');
        $submit ->setAttrib('id', 'saveProfile')
             ->setAttrib('class', 'btn');


        $this->addElements(array($avatar, $firstName, $lastName, $birthday, $language, $email, $timeZone, $username, $intro, $submit));

    }
}

?>