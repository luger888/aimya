<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if($identity){// if no identity - show main page layout

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
        $mail = new Aimya_Mail();
        $mail->setRecipient('morbes77@gmail.com');
        $mail->setTemplate(Aimya_Mail::SIGNUP_ACTIVATION);
        $mail->firstname = 1;
        $mail->lastname = 1;
        $mail->email = 1;
        $mail->password = 1;
        $mail->token = 1;
        $mail->baseLink = "http://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
       // $mail->send();
    }


}

