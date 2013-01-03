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
        $gateway = $payPalModel->getGateway();

        $obj = new Aimya_PayPal_RecurringPayment;

        $obj->environment = $gateway['testMode'];	// or 'beta-sandbox' or 'live'
        $obj->paymentType = urlencode('Authorization');				// or 'Sale' or 'Order'

        // Set request-specific fields.
        $obj->startDate = urlencode("2013-01-04T0:0:0");
        $obj->billingPeriod = urlencode("Month");				// or "Day", "Week", "SemiMonth", "Year"
        $obj->billingFreq = urlencode("1");						// combination of this and billingPeriod must be at most a year
        $obj->paymentAmount = urlencode('30');
        $obj->totalBillingCycles = urlencode('10');
        $obj->currencyID = urlencode('USD');							// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

        /* PAYPAL API  DETAILS */
        $obj->API_UserName = urlencode($gateway['apiUsername']);
        $obj->API_Password = urlencode($gateway['apiPassword']);
        $obj->API_Signature = urlencode($gateway['apiSignature']);
        $obj->API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";

        /*SET SUCCESS AND FAIL URL*/
        $obj->returnURL = urlencode($gateway['returnUrl']);
        $obj->cancelURL = urlencode($gateway['cancelUrl']);


        if(!$_GET['task']) {
            $task="setExpressCheckout"; //set initial task as Express Checkout
        } else {
            $task=$_GET['task'];
        }

        switch($task)
        {
            case "setExpressCheckout":
                $obj->setExpressCheckout();
                exit;
            case "getExpressCheckout":
                $result = $obj->getExpressCheckout();
                if($result['ACK'] == 'Success') {
                    if($this->getRequest()->getParam('userId')){
                        $subscriptionTable = new Application_Model_DbTable_Subscriptions();
                        $ifExistAccount = $subscriptionTable->getSubscription($this->getRequest()->getParam('userId'));

                        if($ifExistAccount) {
                            $subscriptionTable->updateSubscription($ifExistAccount['id'], 'paid');
                        } else {
                            $subscriptionTable->createSubscription($this->getRequest()->getParam('userId'), $obj->paymentAmount);
                        }
                    } else {
                        echo 'fail';
                        die;
                    }
                }
                exit;
            case "error":
                echo "setExpress checkout failed";
                exit;
        }
    }

    public function subscribenewAction()
    {
        $payPalModel = new Application_Model_PayPal();
        $gateway = $payPalModel->getGatewayNew();

        Aimya_PayPal_RecurringPayment_PaypalConfiguration::username($gateway['apiUsername']);
        Aimya_PayPal_RecurringPayment_PaypalConfiguration::password($gateway['apiPassword']);
        Aimya_PayPal_RecurringPayment_PaypalConfiguration::signature($gateway['apiSignature']);
        Aimya_PayPal_RecurringPayment_PaypalConfiguration::business_name( 'Aimya Store' );

        Aimya_PayPal_RecurringPayment_PaypalConfiguration::return_url($gateway['returnUrl']);
        Aimya_PayPal_RecurringPayment_PaypalConfiguration::cancel_url($gateway['cancelUrl']);
        Aimya_PayPal_RecurringPayment_PaypalConfiguration::notify_url($gateway['notifyUrl']);


        //Aimya_PayPal_RecurringPayment_PaypalDigitalGoods::environment( 'live' );

        $subscription_details = array(
            'description'        => 'Example Subscription: $10 sign-up fee then $2/week for the next four weeks.',
            'initial_amount'     => '10.00',
            'amount'             => '2.00',
            'period'             => 'Week',
            'frequency'          => '1',
            'total_cycles'       => '4',
        );

        $paypal_subscription = new Aimya_PayPal_RecurringPayment_PaypalSubscription($subscription_details);
        $html = "";
        if( isset( $_GET['paypal'] ) && $_GET['paypal'] == 'cancel' ){

            //$html .= "<script>if (window!=top) {top.location.replace(document.location);}</script>";
            $html .= '<p>Your subscription has been cancelled. <a href="#" target="_top">Try again?</a></p>';

        } elseif( isset( $_GET['paypal'] ) && $_GET['paypal'] == 'paid' ){

            // Process the payment or start the Subscription
            if( isset( $_GET['PayerID'] ) ) {
                $response = $paypal_subscription->process_payment();
            } else {
                $response = $paypal_subscription->start_subscription();
            }

            $html .= '<h3>Payment Complete!</h3>';
            if( isset( $_GET['PayerID'] ) ) {
                $html .= "<p>Your Transaction ID is {$response['PAYMENTINFO_0_TRANSACTIONID']}</p>";
                $html .= '<p>You can use this Transaction ID to see the details of your subscription like so:</p>';
            } else {
                $html .= '<p>You can use this Profile ID to see the details of your subscription like so:</p>';
            }

        }
        $this->view->html = $html;
        $this->view->obj = $paypal_subscription;
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