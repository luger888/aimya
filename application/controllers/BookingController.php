<?php

class BookingController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this->_helper->AjaxContext()
            ->addActionContext('index', 'json')
            ->addActionContext('add', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $this->view->headLink()->appendStylesheet('../../js/fullcalendar/fullcalendar.css');
        $this->view->headLink()->appendStylesheet('../../js/fullcalendar/fullcalendar.print.css');
        $this->view->headScript()->appendFile('../../js/fullcalendar/fullcalendar.min.js');
        $identity = Zend_Auth::getInstance()->getIdentity();
        $bookingDbTable = new Application_Model_DbTable_Booking();
        $this->view->bookingForm = new Application_Form_Booking();
        $this->view->booking = $bookingDbTable->getBookingByUser($identity->id);

    }

    public function addAction() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($this->getRequest()->isPost()) {

            if($this->getRequest()->getParam('focus_name')){
                $bookingDbTable = new Application_Model_DbTable_Booking();
                $bookingDbTable->addBooking($this->getRequest()->getParams(), $identity->id);
            }
        }
    }

}

