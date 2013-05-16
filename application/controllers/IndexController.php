<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if($identity){  // if there is identity - do not show main page layout
Zend_Debug::dump($identity);die;
           $this->_helper->redirector('features', 'account');
        }
    }

    public function indexAction()
    {
        $this->view->headScript()->prependFile('/js/jquery/validation/registrationValidation.js');
        $login = new Application_Form_Login();
        $reg = new Application_Form_Registration();

        $this->view->login = $login->getElements();
        $this->view->reg = $reg->getElements();
    }


}

