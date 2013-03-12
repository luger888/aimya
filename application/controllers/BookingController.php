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
        $this->view->booking = $bookingDbTable->getBookingByUser($identity->id);
        $this->view->user_id = $identity->id;
        $this->view->role = $identity->role;
        $userGmt = $userDbTable->getTimeZone($identity->id);
        $this->view->timezone = $userGmt['timezone'];
    }
    public function tztestAction(){
        $tz = 'Europe/Moscow';

        // create the DateTimeZone object for later
        $dtzone = new DateTimeZone($tz);

        // create a DateTime object
        $dtime = new DateTime();


        // convert this to the user's timezone using the DateTimeZone object
        $dtime->setTimeZone($dtzone);

        // print the time using your preferred format
        $time = $dtime->format('g:i A m/d/y');

        Zend_Debug::dump($time) ;
    }
    public function addAction()
    {
        $bookingForm = new Application_Form_Booking();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userDbTable = new Application_Model_DbTable_Users();
        $bookingDbTable = new Application_Model_DbTable_Booking();
        $friendsDb = new Application_Model_DbTable_Friends();
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getParam('validation')) {
                $data = $this->getRequest()->getParams();
                if ($bookingForm->isValid($data)) {
                    $isExist = $bookingDbTable->isExistBooking(null, $identity->id, $this->getRequest()->getParam('started_at'), $this->getRequest()->getParam('duration'));
                    if (!$isExist) {
                        $isBlocked = $friendsDb->isBlocked($this->getRequest()->getParam('recipient_id'));
                        if (!$isBlocked) {
                            $this->view->validation = 1;
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

                $this->getRequest()->setParam('creator_tz', $userGmt['timezone']);
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
                    $bookingDbTable = new Application_Model_DbTable_Booking();
                    $bookingDbTable->approveBooking($this->getRequest()->getParam('booking_id'), $identity->id);
                    $this->view->approved = 1;
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

