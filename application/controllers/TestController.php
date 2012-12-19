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
        $doCheck->setLogFile('./img/paypal.txt');
        $data = array();

        if($isPaid){
            $data['skill']= 'success';
            $dbSkills->createSkill($data, 53);
        }else{
            $data['skill']= 'fail';

            $dbSkills->createSkill($data, 54);
        }
    }

    public function responsenewAction()
    {

        ini_set('log_errors', true);
        ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

        $dbSkills = new Application_Model_DbTable_ResumeSkills();

        $listener = new Aimya_PayPal_IpnListener();
        // tell the IPN listener to use the PayPal test sandbox
        $listener->use_sandbox = true;

        // try to process the IPN POST
        try {
            $listener->requirePostMethod();
            $verified = $listener->processIpn();
        } catch (Exception $e) {
            $this->writeLog($e->getMessage());
            exit(0);
        }

        if($verified){
            $this->writeLog("VALID IPN");
            $this->writeLog($listener->getTextReport());
            $data['skill']= 'success';
            $dbSkills->createSkill($data, 53);
        }else{
            $this->writeLog("INVALID IPN");
            $this->writeLog($listener->getTextReport());
            $data['skill']= 'fail';

            $dbSkills->createSkill($data, 54);
        }
    }

    public function writeLog($data)
    {

        if( $fh = @fopen("./img/paypal.txt", "a+") )
        {

            $data = print_r($data, 1);
            fwrite($fh, $data);
            fclose( $fh );
            return( true );
        }
        else
        {
            return( false );
        }

    }
}

