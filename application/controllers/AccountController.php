<?php

class AccountController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        $profile = new Application_Form_Profile();
        $this->view->profile = $profile;
    }

}