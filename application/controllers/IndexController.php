<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {


        $this->view->headScript()->appendFile('../../js/jquery/validation/registrationValidation.js');
        $login = new Application_Form_Login();
        $reg = new Application_Form_Registration();

        $this->view->login = $login->getElements();
        $this->view->reg = $reg->getElements();
    }


}

