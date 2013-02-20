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
        $notify->addMultiOption($i, 'I want to notify me when I receive ' . $i . ' star lesson rating');
        }
        /* END -- Teacher Star Notifications */

        /* Alerts */
        $alert = new Zend_Form_Element_MultiCheckbox('alert');
        $alert->setDecorators($this->basicDecorators);
        $alert->addMultiOption('friend', 'Receive Alert when I have a new friend request');
        $alert->addMultiOption('message', 'Receive Alert when I have a new message');
//        if($identity->role == $this->student){
//            $alert->addMultiOption('refund', 'Receive Alert when Refund is made to me');
//        }
//        if($identity->role != $this->student){
//            $alert->addMultiOption('payment', 'Receive Alert when payment is made to me');
//            $alert->addMultiOption('review', 'Receive Alert when someone wrote lesson review on my page');
//        }
        /* END -- Alerts */


        /* Change password */
        $oldPassword = new Zend_Form_Element_Password('oldPassword');
        $oldPassword//->addValidator('stringLength', false, array(6, 200))
            ->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setErrorMessages(array('Insert your old password'));

        $newPassword = new Zend_Form_Element_Password('newPassword');
        $newPassword ->addValidator('stringLength', false, array(6, 200));
        $newPassword->addValidator('regex', true,
            array(
                'pattern' => '/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/',
                'messages' => array(
                    'regexNotMatch' => "Your password must contain letters and numbers.",
                )
            )
        );
        $newPassword->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setErrorMessages(array('Insert your password'));

        $newPasswordConfirm = new Zend_Form_Element_Password('newPasswordConfirm');
        $newPasswordConfirm//->addValidator('stringLength', false, array(6, 200))
            ->setAttrib('class', 'clearInput required')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->setErrorMessages(array('Insert your password'));


        $submit = new Zend_Form_Element_Submit('saveNotifications');
        $submit ->setLabel('save')
            ->setAttrib('class', 'button-2 save')
            ->setAttrib('id', 'saveNotifications')
            ->setDecorators($this->basicDecorators);



        $this->addElement($alert);
        $this->addElements(array($oldPassword, $newPassword, $newPasswordConfirm));
        if($identity->role != $this->student){
            $this->addElement($notify);
        }
        $this->addElement($submit);
    }


}

?>