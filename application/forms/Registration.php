<?php


class Application_Form_Registration extends Zend_Form
{
    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init(){

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

        $userName = new Zend_Form_Element_Text('userName');
        $userName->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('placeholder', 'User Name')
            ->setAttrib('class', 'required')
            ->setAttrib('id', 'userName')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress())
            ->setAttrib('placeholder', 'E-mail')
            ->setAttrib('placeholder', 'email address')
            ->setAttrib('class', 'clearInput required email')
            ->setDecorators($this->basicDecorators);

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
            ->addValidator('NotEmpty')
            ->addValidator('stringLength', false, array(6, 200))
            ->setAttrib('placeholder', 'Password')
            ->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $password2 = new Zend_Form_Element_Password('password2');
        $password2->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('placeholder', 'Confirm password')
            ->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $type = new Zend_Form_Element_Radio('type');
        $type->addMultiOptions(array(

                'teaching' => 'Teaching member',
                'learning' => 'Learning member'

            )
        );
        $type->setRequired(true)
            ->setAttrib('class', 'styledCheckbox required')
            ->setDecorators($this->basicDecorators)
            ->setSeparator('');

        $submit = new Zend_Form_Element_Submit('SignUp');
        $submit ->setAttrib('id', 'SignUp')
                ->setAttrib('class', 'btn');

        $this->addElements(array($firstName, $lastName, $userName, $email, $password, $password2, $type, $submit));
    }

}