<?php

class Application_Form_Booking extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();//user identity

        $profileModel = new Application_Model_Profile();
        $friendsArray = $profileModel->getFriends($identity->id);//pull friends from db

        $friends = array();
        foreach($friendsArray as $value){
            $friends[$value['id']] = $value['username'];
        }

        /*  BOOKING FORM */
        $this->setName('booking');



        $recipiend_id = new Zend_Form_Element_Select('recipiend_id');
        $recipiend_id->setAttrib('id', 'recipiend_id')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);
        foreach ($friendsArray as  $value) {
            $recipiend_id->addMultiOption($value['id'], $value['username']);
        }
        $start_at = new Zend_Form_Element_Text('started_at');
        $start_at ->setAttrib('id', 'started_at')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);


        $focus_name = new Zend_Form_Element_Text('focus_name');
        $focus_name ->setAttrib('id', 'focus_name')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $rate = new Zend_Form_Element_Text('rate');
        $rate ->setAttrib('class', 'required input-small')
            ->setAttrib('placeholder', 'rate')
            ->setAttrib('id', 'rate')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $duration = new Zend_Form_Element_Select('duration');
        $duration->setAttrib('id', 'duration')
            ->setAttrib('class', 'input-small')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array('15'   => '15 min',
            '45'   => '45 min',
            '60'   => 'hour',
            '0'   => 'lesson'
        ));

        $video = new Zend_Form_Element_Checkbox('video');
        $video->setAttrib('class', 'checkboxlist')
            ->setAttrib('id', 'video')
            ->setDecorators($this->basicDecorators);
        $feedback = new Zend_Form_Element_Checkbox('feedback');
        $feedback->setAttrib('class', 'checkboxlist')
            ->setAttrib('id', 'feedback')
            ->setDecorators($this->basicDecorators);
        $notes = new Zend_Form_Element_Checkbox('notes');
        $notes->setAttrib('class', 'checkboxlist')
            ->setAttrib('id', 'notes')
            ->setDecorators($this->basicDecorators);

        $info = new Zend_Form_Element_Textarea('add_info');
        $info->setAttrib('id', 'add_info')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            -> setAttrib('rows', '2');

    if($recipiend_id == $identity->id){// if recipient
        $submit = new Zend_Form_Element_Submit('sendBooking');
        $submit ->setLabel('Confirm')
            ->setAttrib('id', 'sendBooking')
            ->setDecorators($this->basicDecorators);
    }else{
        $submit = new Zend_Form_Element_Submit('sendBooking');
        $submit ->setLabel('Send for verification')
            ->setAttrib('id', 'sendBooking')
            ->setDecorators($this->basicDecorators);
    }


        $this->addElements(array($recipiend_id,  $start_at,  $focus_name, $rate, $duration, $video, $feedback, $notes, $info, $submit ));

    }
}

?>