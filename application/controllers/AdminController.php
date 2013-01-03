<?php
class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
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