<?php

class Application_Form_Notifications extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');
    /*Roles*/
    private  $teacher = '2';
    private  $student = '1';
    public function init()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();

        /* Teacher Star Notifications */
        $notify = new Zend_Form_Element_MultiCheckbox('notify');
        $notify->setDecorators($this->basicDecorators);
        for ($i=5;$i>0;$i--) {
        $notify->addMultiOption($i, 'I want to notify me when I receive ' . $i . ' star rating');
        }
        /* END -- Teacher Star Notifications */

        /* Alerts */
        $alert = new Zend_Form_Element_MultiCheckbox('alert');
        $alert->setDecorators($this->basicDecorators);
        $alert->addMultiOption('email', 'Receive Alerts via Email');
        $alert->addMultiOption('message', 'Receive Alert when I have a new message');
        $alert->addMultiOption('payment', 'Receive Alert when payment is made to me');
        if($identity->role == $this->student){
            $alert->addMultiOption('review', 'Receive Alert when someone wrote review on my page');
        }
        /* END -- Alerts */


        /* Change password */
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

        if($identity->role == $this->teacher){
            $this->addElement($notify);
        }

        $this->addElements(array(/*$oldPassword, $newPassword, $newPasswordConfirm,*/ $alert, $submit));


    }


}

?>