<?php

class LessonController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }

    public function setupAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function detailsAction()
    {
        $this->_helper->layout()->disableLayout();
    }

}

