<?php

class Application_Form_Profile extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $firstName = new Zend_Form_Element_Text('firstName');
        $firstName->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('placeholder', 'First Name')
            ->setAttrib('class', 'required')
            ->setAttrib('id', 'firstName')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $lastName = new Zend_Form_Element_Text('lastName');
        $lastName ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('class', 'required')
            ->setAttrib('placeholder', 'Last Name')
            ->setAttrib('id', 'lastName')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $birthday = new Zend_Form_Element_Hidden('birthday');
        $birthday ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('id', 'birthday')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $language = new Zend_Form_Element_Text('language');
        $language ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('placeholder', 'Language Spoken')
            ->setAttrib('id', 'language')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $initials = new Zend_Form_Element_Text('initials');
        $initials ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('id', 'initials')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $email = new Zend_Form_Element_Text('email');
        $email->addValidator(new Zend_Validate_EmailAddress())
            ->setAttrib('placeholder', 'E-mail')
            ->setAttrib('placeholder', 'email address')
            ->setAttrib('class', 'clearInput required email')
            ->setDecorators($this->basicDecorators);

        $timeZone = new Zend_Form_Element_Select('timeZone');
        $timeZone->addValidator('NotEmpty')
            ->setAttrib('id', 'timeZone')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $intro = new Zend_Form_Element_Textarea('intro');
        $intro->addValidator('NotEmpty')
            ->setAttrib('id', 'intro')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $submit = new Zend_Form_Element_Submit('saveProfile');
        $submit ->setAttrib('id', 'saveProfile');

        $this->addElements(array($firstName, $lastName, $birthday, $language, $email, $timeZone, $initials, $intro, $submit));

    }
}

?>