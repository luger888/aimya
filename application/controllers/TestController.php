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
        $dbSkills = new Application_Model_DbTable_ResumeSkills();
        $doCheck->setLogFile('logfile.txt');
        $data = array();
        $data['skill']= 'paypal';
        if($isPaid == true){
            $dbSkills->createSkill($data, 53);
        }else{
            $dbSkills->createSkill($data, 54);
        }
    }
}

