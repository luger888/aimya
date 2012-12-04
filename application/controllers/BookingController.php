<?php

class BookingController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this->_helper->AjaxContext()
            ->initContext('json');
    }

    public function indexAction()
    {
        $this->view->headLink()->appendStylesheet('../../js/fullcalendar/fullcalendar.css');
        $this->view->headLink()->appendStylesheet('../../js/fullcalendar/fullcalendar.print.css');
        $this->view->headScript()->appendFile('../../js/fullcalendar/fullcalendar.min.js');

    }

    public function addAction() {
        $form = new Application_Form_Booking();

        $this->view->form = $form;

    }

}

