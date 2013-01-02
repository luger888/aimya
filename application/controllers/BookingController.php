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
        $this->view->id = $identity->id;
        $this->view->role = $identity->role;
        $userGmt = $userDbTable->getTimeZone($identity->id);
        $this->view->timezone = $userGmt['timezone'];
    }

    public function addAction()
    {
        $bookingForm = new Application_Form_Booking();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userDbTable = new Application_Model_DbTable_Users();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
                if ($bookingForm->isValid($data)) {
                    if ($identity->role == '1') {
                        $this->getRequest()->setParam('is_sender_teacher', '0');
                    }
                    $userGmt = $userDbTable->getTimeZone($this->getRequest()->getParam('sender_id'));
                    $bookingDbTable = new Application_Model_DbTable_Booking();
                    $this->getRequest()->setParam('creator_tz', $userGmt['timezone']);
                    if($this->getRequest()->getParam('recipient_id')){
                        $bookingDbTable->addBooking($this->getRequest()->getParams(), $identity->id);
                        $this->view->success = 1;
                    }

                }else{
                    $this->view->success = 0;
                    $this->view->errors = $bookingForm->getErrors();
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
                if(!$isExist){
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

                $bookingDbTable = new Application_Model_DbTable_Booking();
                $bookingDbTable->rejectBooking($this->getRequest()->getParam('booking_id'), $identity->id);

            }
        }
    }

    public function cancelAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($this->getRequest()->isPost()) {

            if ($this->getRequest()->getParam('booking_id')) {

                $bookingDbTable = new Application_Model_DbTable_Booking();
                $bookingDbTable->cancelBooking($this->getRequest()->getParam('booking_id'), $identity->id);

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

