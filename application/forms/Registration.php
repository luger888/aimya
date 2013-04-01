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
            ->setAttrib('maxlength', '50')
            ->addFilters($this->basicFilters)
            ->setErrorMessages(array('Insert your first name'))
            ->setLabel('first name:');
            #->setDecorators($this->basicDecorators);

        $lastName = new Zend_Form_Element_Text('lastname');
        $lastName ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('class', 'regTextInput')
            #->setAttrib('placeholder', 'Last Name')
            ->setAttrib('maxlength', '50')
            ->setAttrib('id', 'lastname')
            ->addFilters($this->basicFilters)
            ->setErrorMessages(array('Insert your last name'))
            ->setLabel('last name:');
           # ->setDecorators($this->basicDecorators);

        $gender = new Zend_Form_Element_Radio('gender');
        $gender ->setAttrib('class', 'regRadio')
            ->setAttrib('id', 'gender')
            ->addFilters($this->basicFilters)
            ->setLabel('gender: ')
            ->setSeparator('');
        $gender->addMultiOptions(array(

                'male' => 'male',
                'female' => 'female'

            )
        )
            ->setValue('male');
        # ->setDecorators($this->basicDecorators);

        $userName = new Zend_Form_Element_Text('username');
        $userName->setRequired(true)
            ->addValidator('NotEmpty')
           # ->setAttrib('placeholder', 'User Name')
            ->setAttrib('maxlength', '50')
            ->setAttrib('class', 'regTextInput')
            ->setAttrib('id', 'username')
            ->addFilters($this->basicFilters)
            ->setErrorMessages(array('Insert your username'))
            ->setLabel('login:');
            #->setDecorators($this->basicDecorators);

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress())
            #->setAttrib('placeholder', 'E-mail')
            #->setAttrib('placeholder', 'email address')
            ->setAttrib('maxlength', '50')
            ->setAttrib('class', 'clearInput regTextInput email')
            ->setErrorMessages(array('Insert your email'))
            ->setLabel('email address:');
            #->setDecorators($this->basicDecorators);

        $password = new Zend_Form_Element_Password('password');
        $password -> setRequired(true);
        $password->addValidator('stringLength', true, array(6, 200));
        $password->setErrorMessages(array('Your password must contain letters and numbers, at least 6 characters'));
        $password->addValidator('regex', true,
                array(
                    'pattern' => '/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/',
                        'messages'  => "Your password must contain letters and numbers"

                )
            );
        //$password->getValidator('regex')->setMessage("Your password must contain letters and numbers.");
        $password->setAttrib('class', 'clearInput regTextInput')
            ->addFilters($this->basicFilters)
            //->setErrorMessages(array('Insert your password'))
            #->setDecorators($this->basicDecorators)
            ->setLabel('password:');

        $password2 = new Zend_Form_Element_Password('password2');
        $password2 #->setRequired(true)
            #->addValidator('NotEmpty')
           #->addValidator('stringLength', false, array(6, 200))
            #->setAttrib('placeholder', 'Confirm password')
           # ->setErrorMessages(array('Retype your password'))
            ->setAttrib('class', 'clearInput regTextInput')
            ->addFilters($this->basicFilters)
            ->setLabel('confirm password:');
            #->setDecorators($this->basicDecorators);

        $type = new Zend_Form_Element_Radio('type');
        $type->addMultiOptions(array(

                '2' => '<span class="txt">'.$this->getView()->translate('teaching member').'</span>',
                '1' => '<span class="txt">'.$this->getView()->translate('learning member').'</span>'

            )

        );
        $type->setAttrib('escape',false);
        $type
            ->setDecorators($this->basicDecorators)
            ->setSeparator('');

//        $agreement = new Zend_Form_Element_Checkbox('agreement');
//        $agreement ->setRequired(true)
//            ->setLabel('if you agree on terms of agreement')
//            ->setDecorators($this->basicDecorators);

        $submit = new Zend_Form_Element_Submit('signup');
        $submit ->setAttrib('id', 'signupBtn')
                ->setLabel('sign up')
                ->setAttrib('class', 'btnSignup signup-element button')
                ->setDecorators($this->basicDecorators);

        $this->addElements(array($firstName, $lastName, $gender, $userName, $email, $password, $password2, $type, $submit));
    }

}