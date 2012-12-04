<?php

class Application_Form_Booking extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $identity =  Zend_Auth::getInstance()->getStorage()->read();
        $this->setName('booking');

        $friendTable = new Application_Model_DbTable_UserRelations();

        $friends = $friendTable->getUserRelations($identity->id);

        foreach($friends as $friend) {
            print_r($friend);
        }
        die;

        $filterChain = new Zend_Filter();
        // Create one big image with at most 143x300 pixel

        $filterChain->appendFilter(new Aimya_Filter_File_Resize(array(
        'directory' => './img/uploads/'.$identity->id.'/avatar/base/',
        'width' => 143,
        'height' => 300,
        'keepRatio' => true,
    )));
        // Create a medium image with at most 104x220 pixels
        $filterChain->appendFilter(new Aimya_Filter_File_Resize(array(
            'directory' => './img/uploads/'.$identity->id.'/avatar/medium/',
            'width' => 104,
            'height' => 220,
            'keepRatio' => true,
        )));

        $avatar = new Zend_Form_Element_File('avatar');
        $avatar
            ->setAttrib('id', 'avatar')
            ->addValidator('Size', false, 1024000)
            ->addValidator('Extension', false, 'jpg,png,gif,jpeg')
            ->addFilter($filterChain)
        //->setDestination('./img/uploads/'.$identity->id.'/avatar/')
            ->removeDecorator('DtDdWrapper')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('label');

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

        $gender = new Zend_Form_Element_Radio('gender');
        $gender ->setAttrib('class', 'regRadio')
            ->setAttrib('id', 'gender')
            ->addFilters($this->basicFilters)
            ->setSeparator('');
        $gender->addMultiOptions(array(

                'male' => 'Male',
                'female' => 'Female'

            )
        )
            ->setValue('male');
         #->setDecorators($this->basicDecorators);

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
        $email->addValidator(new Zend_Validate_EmailAddress())
            ->setAttrib('placeholder', 'E-mail')
            ->setAttrib('placeholder', 'email address')
            ->setAttrib('class', 'clearInput required email')
            ->setDecorators($this->basicDecorators);

        $timeZone = new Zend_Form_Element_Select('timezone');
        $timeZone->setAttrib('id', 'timezone')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);
            foreach ($timeZones as  $value) {
              $timeZone->addMultiOption($value['gmt'], $value['name']);
            }
        $intro = new Zend_Form_Element_Textarea('add_info');
        $intro->setLabel('Introduce Yourself')
            ->setAttrib('id', 'intro')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            -> setAttrib('rows', '7');

        $submit = new Zend_Form_Element_Submit('saveProfile');
        $submit ->setLabel('save')
             ->setAttrib('id', 'saveProfile')
            ->setDecorators($this->basicDecorators);


        $this->addElements(array($avatar, $firstName, $lastName, $gender, $birthday, $language, $email, $timeZone, $username, $intro, $submit));

    }
}

?>