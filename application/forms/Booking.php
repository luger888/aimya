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
            $friends[$value['role']] = $value['role'];
        }

        /*  BOOKING FORM */
        $this->setName('booking');

        $role = new Zend_Form_Element_Radio('role');
        $role ->setAttrib('class', 'radio')
            ->setAttrib('id', 'teacher')
            ->addFilters($this->basicFilters)
            ->setSeparator('');
        $role->addMultiOptions(array(

                '2' => 'student',
                '1' => 'teacher'

            )
        );
        # ->setDecorators($this->basicDecorators);


        $recipiend_id = new Aimya_Form_Element_SelectAttribs('recipiend_id');

        $recipiend_id->setAttrib('id', 'recipiend_id')
            ->setAttrib('class', 't-165')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);
            //->setRequired(true);
            //->addValidator('NotEmpty');
        $recipiend_id->addOption('', 'Choose recipient',array());
        foreach ($friendsArray as  $value) {

            $recipiend_id->addOption($value['id'], $value['username'], array('class' => $value['role']));
        }


        $start_at = new Zend_Form_Element_Text('started_at');
        $start_at ->setAttrib('id', 'started_at')
            ->setRequired(true)
            ->addValidator('NotEmpty')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $start_at_time = new Zend_Form_Element_Text('started_at_time');
        $start_at_time ->setAttrib('id', 'started_at_time')
//            ->setRequired(true)
//            ->addValidator('NotEmpty')
            ->setAttrib('class','hasTimepicker')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $focus_name = new Zend_Form_Element_Text('focus_name');
        $focus_name ->setAttrib('id', 'focus_name')
            ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setAttrib('class', 't-165')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $rate = new Zend_Form_Element_Text('rate');
        $rate ->setAttrib('class', 'required input-small')
            ->setRequired(true)
            ->addValidator('NotEmpty')
            ->addValidator('Digits')
            ->setAttrib('id', 'rate')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators);

        $duration = new Zend_Form_Element_Select('duration');
        $duration->setAttrib('id', 'duration')
            ->addFilters($this->basicFilters)
            ->setDecorators($this->basicDecorators)
            ->addMultiOptions(array('15'   => '15 min',
            '30'   => '30 min',
            '45'   => '45 min',
            '60'   => '60 min'
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
            ->setAttrib('class', 'button')
            ->setAttrib('id', 'sendBooking')
            ->setDecorators($this->basicDecorators);
    }


        $this->addElements(array($role, $recipiend_id,  $start_at, $start_at_time,  $focus_name, $rate, $duration, $video, $feedback, $notes, $info, $submit ));

    }
}

?>