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
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userDbTable = new Application_Model_DbTable_Users();
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getParam('focus_name')) {
                if ($identity->role == '1') {
                    $this->getRequest()->setParam('is_sender_teacher', '0');
                }
                $userGmt = $userDbTable->getTimeZone($this->getRequest()->getParam('sender_id'));
                $bookingDbTable = new Application_Model_DbTable_Booking();
                $this->getRequest()->setParam('creator_tz', $userGmt['timezone']);
                $bookingDbTable->addBooking($this->getRequest()->getParams(), $identity->id);
            }
        }
    }

    public function approveAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($this->getRequest()->isPost()) {

            if ($this->getRequest()->getParam('booking_id')) {

                $bookingDbTable = new Application_Model_DbTable_Booking();
                $bookingDbTable->approveBooking($this->getRequest()->getParam('booking_id'), $identity->id);

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

    public function countAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $bookingTable = new Application_Model_DbTable_Booking();
        $bookingCount = $bookingTable->getNewBookingCount($userId);
        $bookings = $bookingTable->getBookingByUser($userId);
        $bookingPaymentStatus = array();
        foreach($bookings as $value){
            $bookingPaymentStatus['status'] = $value['payment_status'];
            $bookingPaymentStatus['id'] = $value['id'];
        }
        $this->view->bookingPaymentStatus = $bookingPaymentStatus;
        $this->view->bookingCount = $bookingCount;

    }

}

