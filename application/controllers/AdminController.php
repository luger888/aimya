<?php
class AdminController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {

    }

    public function paymentsAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function metricsAction()
    {
        $this->_helper->layout()->disableLayout();
    }
}