<?php

class Application_Form_Notifications extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {

        $oldPassword = new Zend_Form_Element_Password('password');
        $oldPassword->addValidator('stringLength', false, array(6, 200))
            ->setAttrib('placeholder', 'Old Password')
            ->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setErrorMessages(array('Insert your old password'));

        $newPassword = new Zend_Form_Element_Password('password');
        $newPassword ->addValidator('stringLength', false, array(6, 200))
            ->setAttrib('placeholder', ' New Password')
            ->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setErrorMessages(array('Insert your password'));

        $newPasswordConfirm = new Zend_Form_Element_Password('password');
        $newPasswordConfirm->addValidator('stringLength', false, array(6, 200))
            ->setAttrib('placeholder', 'Confirm Password')
            ->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setErrorMessages(array('Insert your password'));


        $submit = new Zend_Form_Element_Submit('saveProfile');
        $submit ->setLabel('Save and continue')
            ->setAttrib('id', 'saveProfile')
            ->setAttrib('class', 'btn')
            ->setDecorators($this->basicDecorators);


        $this->addElements(array($oldPassword, $newPassword, $newPasswordConfirm, $submit));

    }
}

?>