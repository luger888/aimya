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
        $isPaid = $doCheck->checkPayment('bmnbnbbnm');
        $dbSkills = new Application_Model_DbTable_ResumeSkills();
        $doCheck->setLogFile('./paypal.txt');
        $data = array();

        if($isPaid == true){
            $data['skill']= 'success';
            $dbSkills->createSkill($data, 53);
        }else{
            $data['skill']= 'fail';

            $dbSkills->createSkill($data, 54);
        }
    }
}

