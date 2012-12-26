<?php
class PaymentController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{


    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            //->addActionContext('ajax', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {

    }

    public function payAction()
    {
        $teacherId = $this->getRequest()->getParam('teacher_id');
        $bookingId = $this->getRequest()->getParam('booking_id');

        if(isset($teacherId) && isset($bookingId)) {
            $payPalModel = new Application_Model_PayPal();
            $xml = $payPalModel->generateXml($teacherId, $bookingId);

            $response = $payPalModel->getAdaptivUrl($xml);

            if($response) {
                $this->_helper->flashMessenger->addMessage(array('failure'=>'Problem with PayPal url'));
                $this->redirect($response);
            } else {
                $this->redirect('/lesson/index');
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('failure'=>'Problem with parameters'));
        }
    }

    public function ipnAction()
    {
        $listener = new Aimya_PayPal_IpnListener();

        $this->writeLog('Yes');

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

            $bookingTable = new Application_Model_DbTable_Booking();
            //$status = $bookingTable->payLesson($bookingId);

        }else{
            $this->writeLog("INVALID IPN");
            $this->writeLog($listener->getTextReport());

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