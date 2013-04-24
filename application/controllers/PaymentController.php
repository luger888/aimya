<?php
class PaymentController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{
    private $videoCost = 3;
    private $notesCost = 2;
    private $feedbackCost = 2;
    private $subscriptionCost = 30;

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('remained', 'json')
            ->addActionContext('downgrade', 'json')
            ->addActionContext('unsubscribe', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $userIdentity = Zend_Auth::getInstance()->getIdentity();

        $emailForm = new Application_Form_PaypalEmail();
        $subscriptionForm = new Application_Form_Subscriptions();

        $profileTable = new Application_Model_DbTable_Profile();
        $email = $profileTable->getPayPalEmail($userIdentity->id);
        if ($email) {
            $this->view->emailForm = $emailForm->populate($profileTable->getPayPalEmail($userIdentity->id));
        } else {
            $this->view->emailForm = $emailForm;
        }


        $this->view->subscriptionForm = $subscriptionForm;
    }

    public function payAction()
    {
        $teacherId = $this->getRequest()->getParam('teacher_id');
        $bookingId = $this->getRequest()->getParam('booking_id');

        if (isset($teacherId) && isset($bookingId)) {
            $payPalModel = new Application_Model_PayPal();
            $bookingTable = new Application_Model_DbTable_Booking();
            $booking = $bookingTable->getItem($bookingId);

            $rate = $booking['rate'];
            $aimyaProfit = 0;
            if ($booking['video'] == 1) {
                $aimyaProfit += $this->videoCost;
            }
            if ($booking['notes'] == 1) {
                $aimyaProfit += $this->notesCost;
            }
            if ($booking['feedback'] == 1) {
                $aimyaProfit += $this->feedbackCost;
            }
            $userProfit = $rate - $aimyaProfit;

            $xml = $payPalModel->generateXml($teacherId, $bookingId, $userProfit, $aimyaProfit);

            $response = $payPalModel->getAdaptivUrl($xml);

            if ($response) {
                $paymentTable = new Application_Model_DbTable_Orders();

                $isAlreadyExist = $paymentTable->getPayKeyFromOrder($bookingId);

                if (!$isAlreadyExist) {
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
            $this->_helper->flashMessenger->addMessage(array('failure' => 'Problem with parameters'));
        }
    }

    public function ipnAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

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

        if ($verified) {
            $this->writeLog("VALID IPN");
            $this->writeLog($listener->getTextReport());
            if ($_GET['booking_id']) {
                $bookingId = $_GET['booking_id'];
                $bookingTable = new Application_Model_DbTable_Booking();
                $orderTable = new Application_Model_DbTable_Orders();
                $payKey = $orderTable->getPayKeyFromOrder($bookingId);
                if ($payKey['pay_key'] = $_POST['pay_key']) {
                    $orderTable->updatePaymentStatus($bookingId);
                    $bookingTable->payLesson($bookingId);
                }
            }

        } else {
            $this->writeLog("INVALID IPN");
            $this->writeLog($listener->getTextReport());

        }
    }

    public function subsipnAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //$this->writeLog('------------------------------');
        $listener = new Aimya_PayPal_IpnListener();

        // tell the IPN listener to use the PayPal test sandbox
        $listener->use_sandbox = true;

        // try to process the IPN POST
        try {
            $listener->requirePostMethod();
            $verified = $listener->processIpn();
            //$this->writeLog($verified);
        } catch (Exception $e) {
            $this->writeLog($e->getMessage());
            exit(0);
        }

        if ($verified) {
            if ($_GET['subscription_id']) {
                //$this->writeLog($_GET['subscription_id']);
                $subscriptionId = $_GET['subscription_id'];
                //$userId = $_GET['user_id'];
                $subscriptionTable = new Application_Model_DbTable_Subscriptions();
                $payKey = $subscriptionTable->getPayKeyFromOrder($subscriptionId);
                $userId = $payKey['user_id'];
                if ($payKey['pay_key'] = $_POST['pay_key']) {
                    $res = $subscriptionTable->updateSubscriptionStatus($subscriptionId);
                    if($res) {
                        $userTable = new Application_Model_DbTable_Users();
                        $user = $userTable->getItem($userId);
                        if($user['role'] == 1) {
                            $userTable->updateRole($userId, 2);
                        }
                    }
                }
            }

        } else {
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

                if ($status) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Email successfully save'));
                    $this->_helper->redirector('index', 'payment');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('failure' => 'Problem with saving. Please try again later'));
                    $this->_helper->redirector('index', 'payment');
                }
            } else {
                $this->_helper->flashMessenger->addMessage(array('failure' => 'Please insert corect email'));
                $this->_helper->redirector('index', 'payment');
            }
        }
    }

    public function subscribeAction()
    {
        $period = $this->getRequest()->getParam('period');

        if (isset($period)) {
            $payPalModel = new Application_Model_PayPal();

            $subscriptionTable = new Application_Model_DbTable_Subscriptions();

            $aimyaProfit = $period * $this->subscriptionCost;

            $lastId = $subscriptionTable->getLastSubscriptionId() + 1;

            $requestData = $payPalModel->generateSubscriptionXml($lastId, $aimyaProfit);
            if($period >= 8 && $period <= 12) $period = 12;

            $response = $payPalModel->getAdaptivUrl($requestData);

            if ($response) {

                $isAlreadyExist = $subscriptionTable->getPayKeyFromOrder($lastId);

                if (!$isAlreadyExist) {

                    $latestSubscription = $subscriptionTable->getLatestSubscription(Zend_Auth::getInstance()->getIdentity()->id);
                    $activeToDate = $latestSubscription['maxId'];

                    $current = new DateTime();
                    $activeTo = new DateTime($activeToDate);

                    if($current < $activeTo) {
                        $activeToField = date('Y-m-d H:i:s', strtotime($activeToDate . " +$period month"));
                    } else {
                        $activeToField = date('Y-m-d h:i:s', strtotime("+$period month"));
                    }

                    $data = array(
                        'user_id' => Zend_Auth::getInstance()->getIdentity()->id,
                        'aimya_profit' => $aimyaProfit,
                        'pay_key' => $response['pay_key'],
                        'status' => 'pending',
                        'active_to' => $activeToField,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );

                    $subscriptionTable->createSubscription($data);
                }

                $this->redirect($response['url']);
            } else {
                $this->redirect('/payment');
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('failure' => 'Problem with parameters'));
        }
    }

    public function unsubscribeAction()
    {
        if ($this->getRequest()->isPost()) {
            $user_id = Zend_Auth::getInstance()->getIdentity()->id;
            $refundDb = new Application_Model_DbTable_Refund();
            $subscriptionDb = new Application_Model_DbTable_Subscriptions();
            if ($this->getRequest()->getParam('unsubscribeRequest')) {
                if ($subId = $subscriptionDb->isRefundEnable()) {
                    {
                        $result = $refundDb->createRefund($subId, $user_id);
                    }
                }

            }
            if ($this->getRequest()->getParam('cancelRefund')) {

                $refundDb->cancelRefund($this->getRequest()->getParam('cancelRefund'));
            }
            if ($this->getRequest()->getParam('approveRefund')) {
                $refundForm = new Application_Form_Refund();
                $data = $this->getRequest()->getPost();
                if($refundForm->isValid($data)) {

                    $period = $this->getRequest()->getParam('period');
                    $amount = $this->getRequest()->getParam('amount');
                    $requestComment = $this->getRequest()->getParam('request_comment');
                    $subscriptionId = $this->getRequest()->getParam('subscription_id');

                    $latestSubscription = $subscriptionDb->getItem($subscriptionId);
                    $activeToDate = $latestSubscription['active_to'];
                    $aimyaProfit = $latestSubscription['aimya_profit'];
                    $userId = $latestSubscription['user_id'];

                    $newAmount = $aimyaProfit - $amount;
                    $activeTo = date('Y-m-d H:i:s', strtotime($activeToDate . " -$period month"));

                    $result = $subscriptionDb->refundSubscription($subscriptionId, $user_id, $activeTo, $newAmount);
                    if($result) {
                        $refundDb->approveRefund($this->getRequest()->getParam('approveRefund'));
                        $mesageTable = new Application_Model_DbTable_Message();
                        $data = array(
                            'sender_id' => $user_id,
                            'recipient_id' => $userId,
                            'content' => $requestComment,
                            'subject' => 'Refund',
                        );
                        $mesageTable->sendMessage($data);
                        $this->view->status = 'success';
                    } else {
                        $this->view->status = 'error';
                    }
                } else{
                    $this->view->status = 'error';
                    $this->view->errors = $refundForm->getErrors();
                }
                //$subscriptionDB->refundSubscription($this->getRequest()->getParam('approveRefund'))

            }
        }

    }

    public function remainedAction()
    {

        $subscriptionTable = new Application_Model_DbTable_Subscriptions();
        $activeTo = $subscriptionTable->getTimeLeft();
        if ($activeTo['active_to'] == NULL && Zend_Auth::getInstance()->getIdentity()->role > 1) {
            $subscriptionTable->setDefaultPeriod();
        }

        $now = time(); // or your date as well
        $your_date = strtotime($activeTo['active_to']);
        $datediff = $now - $your_date;
        $timeLeft = floor($datediff / (60 * 60 * 24));
        $timeLeft = substr($timeLeft, 1);

        if ($timeLeft <= 5) {
            $this->view->status = 'success';
            $this->view->timeLeft = $timeLeft;
        } else {
            $this->view->status = 'failure';
        }
    }

    public function upgradeAction()
    {
        $subscriptionForm = new Application_Form_Subscriptions();

        $this->view->subscriptionForm = $subscriptionForm;
    }

    public function downgradeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $userId = Zend_Auth::getInstance()->getIdentity()->id;

            $userTable = new Application_Model_DbTable_Users();
            $result = $userTable->updateRole($userId, '1');
            if($result) {
                $this->view->answer = 'success';
            } else {
                $this->view->answer = 'error';
            }
        }

    }

    public function writeLog($data)
    {

        if ($fh = @fopen("./img/paypal.txt", "a+")) {

            $data = print_r($data, 1);
            fwrite($fh, $data);
            fclose($fh);
            return (true);
        }
        else
        {
            return (false);
        }

    }
}