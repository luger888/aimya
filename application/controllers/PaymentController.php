<?php
class PaymentController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{
    private $videoCost = 3;
    private $notesCost = 2;
    private $feedbackCost = 2;

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            //->addActionContext('ajax', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $emailForm = new Application_Form_PaypalEmail();
        $subscriptionForm = new Application_Form_Subscriptions();
        $profileTable = new Application_Model_DbTable_Profile();
        $email = $profileTable->getPayPalEmail($userId);
        if($email) {
            $this->view->emailForm = $emailForm->populate($profileTable->getPayPalEmail($userId));
        } else {
            $this->view->emailForm = $emailForm;
        }
        $this->view->subscriptionForm = $subscriptionForm;
    }

    public function payAction()
    {
        $teacherId = $this->getRequest()->getParam('teacher_id');
        $bookingId = $this->getRequest()->getParam('booking_id');

        if(isset($teacherId) && isset($bookingId)) {
            $payPalModel = new Application_Model_PayPal();
            $bookingTable = new Application_Model_DbTable_Booking();
            $booking = $bookingTable->getItem($bookingId);

            $rate = $booking['rate'];
            $aimyaProfit = 0;
            if($booking['video'] == 1) {
                $aimyaProfit += $this->videoCost;
            }
            if($booking['notes'] == 1) {
                $aimyaProfit += $this->notesCost;
            }
            if($booking['feedback'] == 1) {
                $aimyaProfit += $this->feedbackCost;
            }
            $userProfit = $rate - $aimyaProfit;

            $xml = $payPalModel->generateXml($teacherId, $bookingId, $userProfit, $aimyaProfit);

            $response = $payPalModel->getAdaptivUrl($xml);

            if($response) {
                $paymentTable = new Application_Model_DbTable_Orders();

                $isAlreadyExist = $paymentTable->getPayKeyFromOrder($bookingId);

                if(!$isAlreadyExist) {
                    $data = array(
                        'payer_id' => Zend_Auth::getInstance()->getIdentity()->id,
                        'seller_id' => $teacherId,
                        'booking_id' => $bookingId,
                        'aimya_profit' => $aimyaProfit,
                        'teacher_profit' => $userProfit,
                        'pay_key' => $response['pay_key'],
                        'status' => 'pending',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );

                    $paymentTable->addPayment($data);
                }

                $this->redirect($response['url']);
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
            if($_GET['booking_id']) {
                $bookingId = $_GET['booking_id'];
                $bookingTable = new Application_Model_DbTable_Booking();
                $orderTable = new Application_Model_DbTable_Orders();
                $payKey = $orderTable->getPayKeyFromOrder($bookingId);
                if($payKey['pay_key'] = $_POST['pay_key']) {
                    $orderTable->updatePaymentStatus($bookingId);
                    $bookingTable->payLesson($bookingId);
                }
            }

        }else{
            $this->writeLog("INVALID IPN");
            $this->writeLog($listener->getTextReport());

        }
    }

    public function emailAction()
    {
        $form = new Application_Form_PaypalEmail();
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                    $userId = Zend_Auth::getInstance()->getIdentity()->id;
                    $profileTable = new Application_Model_DbTable_Profile();
                    $status = $profileTable->updatePaypalEmail($formData['paypal_email'], $userId);

                    if($status) {
                        $this->_helper->flashMessenger->addMessage(array('success'=>'Email successfully save'));
                        $this->_helper->redirector('index', 'payment');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('failure'=>'Problem with saving. Please try again later'));
                        $this->_helper->redirector('index', 'payment');
                    }
            } else {
                $this->_helper->flashMessenger->addMessage(array('failure'=>'Please insert corect email'));
                $this->_helper->redirector('index', 'payment');
            }
        }
    }

    public function subscribeAction()
    {
        $payPalModel = new Application_Model_PayPal();

        $rate = $booking['rate'];
        $aimyaProfit = 0;
        if($booking['video'] == 1) {
            $aimyaProfit += $this->videoCost;
        }
        if($booking['notes'] == 1) {
            $aimyaProfit += $this->notesCost;
        }
        if($booking['feedback'] == 1) {
            $aimyaProfit += $this->feedbackCost;
        }
        $userProfit = $rate - $aimyaProfit;

        $xml = $payPalModel->generateXml($userProfit, $aimyaProfit);
    }

    public function unsubscribeAction()
    {

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