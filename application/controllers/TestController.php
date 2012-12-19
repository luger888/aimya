<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $form = new Application_Form_Test();
        $this->view->form = $form;
    }

    public function paypalAction()
    {

    }

    public function responseAction()
    {
        $doCheck = new Aimya_PayPal_Paypal();
        $isPaid = $doCheck->checkPayment($_POST);

        if($isPaid == true){
            $this->view->reponse = 'success';
        }else{

        }$this->view->reponse = 'fault';
    }
}

