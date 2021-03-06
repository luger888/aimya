<?php

class BookingController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('index', 'json')
            ->addActionContext('add', 'json')
            ->addActionContext('count', 'json')
            ->addActionContext('approve', 'json')
            ->addActionContext('cancel', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $userDbTable = new Application_Model_DbTable_Users();
        $this->view->headLink()->appendStylesheet('../../js/fullcalendar/fullcalendar.css');
        $this->view->headScript()->appendFile('../../js/fullcalendar/fullcalendar.js');
        $identity = Zend_Auth::getInstance()->getIdentity();
        $bookingDbTable = new Application_Model_DbTable_Booking();
        $this->view->bookingForm = new Application_Form_Booking();
        $booking = $bookingDbTable->getBookingByUser($identity->id);
        $this->view->booking = $booking;
        $this->view->user_id = $identity->id;
        $current_time = $userDbTable->getCurrentTime($identity->id);
        $this->view->current_time = $current_time;
        $this->view->role = $identity->role;
        $userGmt = $userDbTable->getTimeZone($identity->id); //id


        $tzDbTable = new Application_Model_DbTable_TimeZones();
        $tz = $tzDbTable->getItem($userGmt['timezone']);

        $dtzone = new DateTimeZone($tz['code']);

        $dtime = new DateTime();
        $dtime->setTimeZone($dtzone);
        $time = $dtime->getOffset();
        $this->view->timezone = $time;


    }
    public function tztestAction(){
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userDbTable = new Application_Model_DbTable_Users();
        $tzDbTable = new Application_Model_DbTable_TimeZones();
        $userGmt = $userDbTable->getTimeZone($identity->id);
        $tz = $tzDbTable->getTimezoneByGmt($userGmt['timezone']);

        // create the DateTimeZone object for later
        $dtzone = new DateTimeZone($tz['code']);

        // create a DateTime object
        $dtime = new DateTime();


        // convert this to the user's timezone using the DateTimeZone object
        $dtime->setTimeZone($dtzone);

        // print the time using your preferred format
        $time = $dtime->getOffset();

        Zend_Debug::dump($dtime) ;
    }
    public function addAction()
    {
        $bookingForm = new Application_Form_Booking();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userDbTable = new Application_Model_DbTable_Users();
        $bookingDbTable = new Application_Model_DbTable_Booking();
        $profileDbTable = new Application_Model_DbTable_Profile();
        $friendsDb = new Application_Model_DbTable_Friends();
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getParam('validation')) {
                $data = $this->getRequest()->getParams();
                if ($bookingForm->isValid($data)) {
                    $isExist = $bookingDbTable->isExistBooking(null, $identity->id, $this->getRequest()->getParam('started_at'), $this->getRequest()->getParam('duration'));
                    if (!$isExist) {
                        $isBlocked = $friendsDb->isBlocked($this->getRequest()->getParam('recipient_id'));
                        if (!$isBlocked) {
                            $isExistPaypalEmail = $profileDbTable->getPayPalEmail($identity->id);
//                            if($this->getRequest()->getParam('is_sender_teacher') == '1' && $isExistPaypalEmail['paypal_email'] == '' && $identity->role != '1'){ ::PAYMENT ROLLBACK::
//                                $this->view->emailerror = 1;
//                                $this->view->check = $this->getRequest()->getParam('is_sender_teacher');
//                            }else{
                                $this->view->validation = 1;
//                            }

                            if ($identity->role == '1') {
                                $this->view->role = 0;
                            }else{
                                $this->view->role = $this->getRequest()->getParam('is_sender_teacher');
                            }

                        } else {
                            $this->view->blocked = 1;
                        }

                    } else {
                        $this->view->fail = 1;
                    }


                } else {
                    $this->view->success = 0;
                    $this->view->errors = $bookingForm->getErrors();
                }
            }
            if ($this->getRequest()->getParam('approve')) {
                if ($identity->role == '1') {
                    $this->getRequest()->setParam('is_sender_teacher', '0');
                }
                $userGmt = $userDbTable->getTimeZone($this->getRequest()->getParam('sender_id'));

                $tzDbTable = new Application_Model_DbTable_TimeZones(); /*TimeZones object*/
                $tz = $tzDbTable->getItem($userGmt['timezone']);/*get timezone code for php*/
                $dtzone = new DateTimeZone($tz['code']);/*creating timezone object with given code from db*/
                $dtime = new DateTime();
                $dtime->setTimeZone($dtzone);/*setting timezone object to time object*/
                $time = $dtime->getOffset();/*getting UTC offset in UNIX-stamp*/
                $this->getRequest()->setParam('creator_tz', $time);
                if ($this->getRequest()->getParam('recipient_id')) {
                    $isExist = $bookingDbTable->isExistBooking(null, $identity->id, $this->getRequest()->getParam('started_at'), $this->getRequest()->getParam('duration'));
                    if (!$isExist) {
                        $isBlocked = $friendsDb->isBlocked($this->getRequest()->getParam('recipient_id'));
                        if (!$isBlocked) {
                            $bookingDbTable->addBooking($this->getRequest()->getParams(), $identity->id);
                            $this->view->success = 1;
                        }
                    }
                }
            }
        }
    }

    public function approveAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($this->getRequest()->isPost()) {
            $bookingDbTable = new Application_Model_DbTable_Booking();
            if ($this->getRequest()->getParam('booking_id')) {
                $isExist = $bookingDbTable->isExistBooking($this->getRequest()->getParam('booking_id'), $identity->id);
                if (!$isExist) {
//                    $profileDbTable = new Application_Model_DbTable_Profile(); ::PAYMENT ROLLBACK::
//                    $isExistPaypalEmail = $profileDbTable->getPayPalEmail($identity->id);
//                    if($isExistPaypalEmail['paypal_email'] == '' && $identity->role != '1'){  CHECK FOR EMAIL COMMENTED
//                        $this->view->emailerror = 1;
//
//                    }else{
                        $bookingDbTable = new Application_Model_DbTable_Booking();
                        $bookingDbTable->approveBooking($this->getRequest()->getParam('booking_id'), $identity->id);
                        $this->view->approved = 1;
//                    }

                }


            }
        }
    }

    public function rejectAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($this->getRequest()->isPost()) {

            if ($this->getRequest()->getParam('booking_id')) {
                $userTable = new Application_Model_DbTable_Users();
                $bookingDbTable = new Application_Model_DbTable_Booking();

                $bookingItem = $bookingDbTable->getItem($this->getRequest()->getParam('booking_id'));
                $user = $userTable->getItem($this->getRequest()->getParam('sender_id'));

                $messageTable = new Application_Model_DbTable_Message();
                $message = array('sender_id' => $identity->id,
                    'recipient_id' => $this->getRequest()->getParam('sender_id'),
                    'content' => "The booked lesson with " . $user['username'] . " on " . $bookingItem['started_at'] . " has been rejected. Please, reply person for more details.",
                    'subject' => "Lesson has been rejected.");
                $messageTable->sendMessage($message);
                $bookingDbTable->rejectBooking($this->getRequest()->getParam('booking_id'), $identity->id);
            }
        }
    }

    public function cancelAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($this->getRequest()->isPost()) {
            $bookingDbTable = new Application_Model_DbTable_Booking();
            if ($this->getRequest()->getParam('booking_id')) {

                $userTable = new Application_Model_DbTable_Users();

                $bookingDbTable->cancelBooking($this->getRequest()->getParam('booking_id'), $identity->id);
                $bookingItem = $bookingDbTable->getItem($this->getRequest()->getParam('booking_id'));
                $user = $userTable->getItem($this->getRequest()->getParam('recipient_id'));

                $messageTable = new Application_Model_DbTable_Message();
                $message = array('sender_id' => $identity->id,
                    'recipient_id' => $this->getRequest()->getParam('recipient_id'),
                    'content' => "Dear " . $user['username'] . ". I am cancelling Lesson on " . $bookingItem['started_at'] . ". Please, confirm the cancellation and we will reschedule new time for a Lesson at more convenient time upon mutual agreement. In order to do so, please, go to “My Booking”, find the requested Lesson and confirm cancellation. Thank you",
                    'subject' => "Lesson Cancellation!");
                $messageTable->sendMessage($message);
            }
            if ($this->getRequest()->getParam('cancelConfirmId')) {
                $bookingDbTable->cancelBooking($this->getRequest()->getParam('cancelConfirmId'), $identity->id, 'confirm');
                $this->view->confirmed = 1;
            }
            if ($this->getRequest()->getParam('cancelRejectId')) {
                $bookingDbTable->cancelBooking($this->getRequest()->getParam('cancelRejectId'), $identity->id, 'reject');
                $this->view->rejected = 1;
            }
        }
    }

    public function removeAction()
    {
        if ($this->getRequest()->isPost()) {
            $bookingDbTable = new Application_Model_DbTable_Booking();
            if ($this->getRequest()->getParam('booking_id')) {
                $result = $bookingDbTable->removeBooking($this->getRequest()->getParam('booking_id'));
                if ($result) {
                    $this->view->success = 1;
                }
            }
        }
    }

    public function countAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $bookingTable = new Application_Model_DbTable_Booking();
        $bookingCount = $bookingTable->getNewBookingCount($userId);
        $lessonModel = new Application_Model_Lesson();
        $bookingList = $bookingTable->getFullBookingData($userId);
        $extendedBookingList = $lessonModel->extendLesson($bookingList);
        $bookingPaymentStatus = array();
        foreach ($extendedBookingList as $value) {
            if (isset($value['booking']['pay'])) { //if need to append payButton for student
                $bookingPaymentStatus['booking']['pay'] = 1; //request has been sent, show PAY button
                $bookingPaymentStatus['booking']['id'] = $value['booking']['id']; //id of booking
                $bookingPaymentStatus['userdata']['id'] = $value['userData']['id']; //id of bookinguserdata
            }
            if (isset($value['booking']['sendRequest'])) { //if need to append payButton for student
                $bookingPaymentStatus['booking']['send'] = 1; //request has been sent, show Send button
                $bookingPaymentStatus['booking']['id'] = $value['booking']['id']; //id of booking
                $bookingPaymentStatus['userdata']['id'] = $value['userData']['id']; //id of bookinguserdata
            }
            if (isset($value['booking']['paid'])) { //if need to append payButton for student
                $bookingPaymentStatus['booking']['paid'] = 1; //request has been sent, show Send button
                $bookingPaymentStatus['booking']['id'] = $value['booking']['id']; //id of booking
                $bookingPaymentStatus['userdata']['id'] = $value['userData']['id']; //id of bookinguserdata
            }
            if (isset($value['booking']['pending'])) { //if need to append payButton for student
                $bookingPaymentStatus['booking']['pending'] = 1; //request has been sent, show Send button
                $bookingPaymentStatus['booking']['id'] = $value['booking']['id']; //id of booking
                $bookingPaymentStatus['userdata']['id'] = $value['userData']['id']; //id of bookinguserdata
            }
            if (isset($value['booking']['startLesson'])) { //if need to append payButton for student
                $bookingPaymentStatus['booking']['start'] = 1; //request has been sent, show Send button
                $bookingPaymentStatus['booking']['id'] = $value['booking']['id']; //id of booking
                $bookingPaymentStatus['userdata']['id'] = $value['userData']['id']; //id of bookinguserdata
            }
            if (isset($value['booking']['join'])) { //if need to append payButton for student
                $bookingPaymentStatus['booking']['join'] = 1; //request has been sent, show Send button
                $bookingPaymentStatus['booking']['id'] = $value['booking']['id']; //id of booking
                $bookingPaymentStatus['userdata']['id'] = $value['userData']['id']; //id of bookinguserdata
            }
        }
        // Zend_Debug::dump($extendedBookingList);
        $this->view->bookingPaymentStatus = $bookingPaymentStatus;
        $this->view->bookingCount = $bookingCount;

    }


}

