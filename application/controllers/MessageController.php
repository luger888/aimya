<?php
class MessageController extends Zend_Controller_Action
{
    public function init()
    {
        $this   ->_helper->AjaxContext()
            ->initContext('json');
    }

    public function indexAction()
    {

    }

    public function inboxAction()
    {

    }

    public function sentAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function createAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function trashAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function archivedAction()
    {
        $this->_helper->layout()->disableLayout();
    }
}