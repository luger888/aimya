<?php


class Application_Form_Registration extends Zend_Form
{

    //need password and email validators!
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init(){

        $firstName = new Zend_Form_Element_Text('firstname');
        $firstName->setRequired(true)
            ->addValidator('NotEmpty')
            #->setAttrib('placeholder', 'First Name')
            ->setAttrib('class', 'regTextInput')
            ->setAttrib('id', 'firstname')
            ->addFilters($this->basicFilters)
            ->setErrorMessages(array('Insert your first name'))
            ->setLabel('First Name:');
            #->setDecorators($this->basicDecorators);

        $lastName = new Zend_Form_Element_Text('lastname');
        $lastName ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('class', 'regTextInput')
            #->setAttrib('placeholder', 'Last Name')
            ->setAttrib('id', 'lastname')
            ->addFilters($this->basicFilters)
            ->setErrorMessages(array('Insert your last name'))
            ->setLabel('Last Name:');
           # ->setDecorators($this->basicDecorators);

        $gender = new Zend_Form_Element_Radio('gender');
        $gender ->setAttrib('class', 'regRadio')
        #->setAttrib('placeholder', 'Last Name')
            ->setAttrib('id', 'gender')
            ->addFilters($this->basicFilters)
           // ->setErrorMessages(array('Select your gender'))
            ->setLabel('Gender: ')
            ->setSeparator('');
        $gender->addMultiOptions(array(

                'male' => 'male',
                'female' => 'female'

            )
        );
        # ->setDecorators($this->basicDecorators);

        $userName = new Zend_Form_Element_Text('username');
        $userName->setRequired(true)
            ->addValidator('NotEmpty')
           # ->setAttrib('placeholder', 'User Name')
            ->setAttrib('class', 'regTextInput')
            ->setAttrib('id', 'username')
            ->addFilters($this->basicFilters)
            ->setErrorMessages(array('Insert your username'))
            ->setLabel('Login:');
            #->setDecorators($this->basicDecorators);

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress())
            #->setAttrib('placeholder', 'E-mail')
            #->setAttrib('placeholder', 'email address')
            ->setAttrib('class', 'clearInput regTextInput email')
            ->setErrorMessages(array('Insert your email'))
            ->setLabel('Email address:');
            #->setDecorators($this->basicDecorators);

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
            ->addValidator('NotEmpty')
            ->addValidator('stringLength', false, array(6, 200))
            #->setAttrib('placeholder', 'Password')
            ->setAttrib('class', 'clearInput regTextInput')
            ->addFilters($this->basicFilters)
            #->setDecorators($this->basicDecorators)
            ->setLabel('Password:');

        $password2 = new Zend_Form_Element_Password('password2');
        $password2 #->setRequired(true)
            ->addValidator('NotEmpty')
           #->addValidator('stringLength', false, array(6, 200))
            #->setAttrib('placeholder', 'Confirm password')
            ->setAttrib('class', 'clearInput regTextInput')
            ->addFilters($this->basicFilters)
            ->setLabel('Confirm password:');
            #->setDecorators($this->basicDecorators);

        $type = new Zend_Form_Element_Radio('type');
        $type->addMultiOptions(array(

                '2' => 'TEACHING MEMBER',
                '1' => 'LEARNING MEMBER'

            )
        );
        $type
            ->setDecorators($this->basicDecorators)
            ->setSeparator('');

        $submit = new Zend_Form_Element_Submit('signup');
        $submit ->setAttrib('id', 'signup')
                ->setLabel('SIGN UP')
                ->setAttrib('class', 'btnSignup signup-element')
                ->setDecorators($this->basicDecorators);

        $this->addElements(array($firstName, $lastName, $gender, $userName, $email, $password, $password2, $type, $submit));
    }

}