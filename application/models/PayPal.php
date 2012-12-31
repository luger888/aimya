<?php

class Application_Model_PayPal
{

    //private $payPalApiLogin = 'aimSto_1356606163_biz_api1.svitla.com';
    //private $payPalApiPassword = '1356606183';
    //private $payPalSignature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31Aqj87aln5rpg.G3x2sFLvAx-YMjX';
    //private $payPalApiId = 'APP-80W284485P519543T';
    //private $adaptivUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Pay';
    //private $amiyaPayPalEmail = 'aimSto_1356606163_biz@svitla.com';

    private $payPalApiLogin = 'aim_pr_1356696524_biz_api1.mail.ru';
    private $payPalApiPassword = '1356696594';
    private $payPalSignature = 'AGgiSKCZ2j6Msrbwd65ACNRY-hZIAANcIOdlhHhOA5jNQxS7Xf-1Dcj0';
    private $payPalApiId = 'APP-80W284485P519543T';
    private $adaptivUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Pay';
    private $amiyaPayPalEmail = 'aim_pr_1356696524_biz@mail.ru';
    private $testMode = 'sandbox';

    public function generateXml($sellerId, $bookingId, $userProfit, $aimyaProfit) {

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $cancelUrl = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/lesson';
        $returnURL = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/lesson';
        $ipnURL = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/ipn?booking_id=' . $bookingId;

        $profileTable = new Application_Model_DbTable_Profile();
        $bookingTable = new Application_Model_DbTable_Booking();

        $paypalEmail = $profileTable->getPayPalEmail($sellerId);
        //$paypalEmail['paypal_email'] = 'seller_1355909799_biz@gmail.com';


        $body_data  = "<?xml version='1.0'?>";
        $body_data .= "<payRequest>";
        $body_data .= "<actionType>PAY</actionType>";
        $body_data .= "<cancelUrl>{$cancelUrl}</cancelUrl>";
        $body_data .= "<returnUrl>{$returnURL}</returnUrl>";
        $body_data .= "<currencyCode>USD</currencyCode>";
        $body_data .= "<feesPayer>SENDER</feesPayer>";
        $body_data .= "<receiverList>";
        $body_data .= "<receiver>";
        $body_data .= "<amount>{$userProfit}</amount>";
        $body_data .= "<email>{$paypalEmail['paypal_email']}</email>";
        $body_data .= "<invoiceId>{$bookingId}</invoiceId>";
        $body_data .= "</receiver>";
        if($aimyaProfit != 0) {
            $body_data .= "<receiver>";
            $body_data .= "<amount>{$aimyaProfit}</amount>";
            $body_data .= "<email>{$this->amiyaPayPalEmail}</email>";
            $body_data .= "<invoiceId>{$bookingId}</invoiceId>";
            $body_data .= "</receiver>";
        }
        $body_data .= "</receiverList>";
        $body_data .= "<requestEnvelope>";
        $body_data .= "<errorLanguage>en_US</errorLanguage>";
        $body_data .= "</requestEnvelope>";
        $body_data .= "<ipnNotificationUrl>{$ipnURL}</ipnNotificationUrl>";
        $body_data .= "</payRequest>";

        return $body_data;

    }

    public function getAdaptivUrl($xml) {
        $params = array(
            "http" => array(
                "method" => "POST",
                "content" => $xml,

                "header" => "Content-type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen ( $xml ) . "\r\n" .
                    "X-PAYPAL-SECURITY-USERID: {$this->payPalApiLogin}\r\n" .
                    "X-PAYPAL-SECURITY-SIGNATURE: {$this->payPalSignature}\r\n" .
                    "X-PAYPAL-SECURITY-PASSWORD: {$this->payPalApiPassword}\r\n" .
                    "X-PAYPAL-APPLICATION-ID: {$this->payPalApiId}\r\n" .
                    "X-PAYPAL-REQUEST-DATA-FORMAT: XML\r\n" .
                    "X-PAYPAL-RESPONSE-DATA-FORMAT: XML\r\n"
            )
        );

        $ctx = stream_context_create($params);
        $fp = fopen($this->adaptivUrl, "r", false, $ctx);

        $response = stream_get_contents($fp);

        $xmlresponse = simplexml_load_string($response);
        $ack = trim($xmlresponse->responseEnvelope->ack) ;
        $paykey = trim($xmlresponse->payKey);

        fclose($fp);

        if ($ack == 'Success') {
            $data = array(
                'url' => "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $paykey,
                'pay_key' => $paykey
            );
            return $data;
        } else {
            return false;
        }
    }

    public function generateSubscriptionXml($sellerId, $period, $dateFrom) {

        $pp = new Aimya_PayPal_Subscription();

        $pp->test = true; // connect to the test sandbox or live server
        $pp->requestHeaderArr['user'] = $this->payPalApiLogin;
        $pp->requestHeaderArr['pwd'] = $this->payPalApiPassword;
        $pp->requestHeaderArr['signature'] = $this->payPalSignature;
        $pp->requestHeaderArr['countrycode'] = "US";
        $pp->requestHeaderArr['billingperiod'] = "Month"; // bill per month
        $pp->requestHeaderArr['billingfrequency'] = 1; // bill once every month
        $pp->requestHeaderArr['currencycode'] = "USD";
        $pp->requestHeaderArr['amt'] = 9.95; // amount to bill per month
        $pp->requestHeaderArr['initamt'] = 0.00; // setup fee
        $pp->requestHeaderArr['taxamt'] = $pp->requestHeaderArr['amt'] * .07; // 0 for no tax
        $pp->requestHeaderArr['desc'] = "Super Deluxe Package";

        // most likely won't need to edit below here

        $pp->requestHeaderArr['creditcardtype'] = $_REQUEST["cardtype"];
        $pp->requestHeaderArr['acct'] = $_REQUEST["cardnumber"];
        $pp->requestHeaderArr['expdate'] = str_pad($_REQUEST["cardexpm"],2,'0', STR_PAD_LEFT)  .  $_REQUEST["cardexpy"];
        $pp->requestHeaderArr['firstname'] = $_REQUEST["f_nm"];
        $pp->requestHeaderArr['lastname'] = $_REQUEST["l_nm"];
        $pp->requestHeaderArr['profilestartdate'] = gmdate("Y-m-d\TH:i:s\Z");
        $pp->requestHeaderArr['totalbillingcycles'] = $_REQUEST["period"];
        $pp->requestHeaderArr['email'] = $_POST['email'];
        $pp->requestHeaderArr['payerstatus'] = "verified";

        $ppResponse = $pp->PPHttpPost(); // make the connection to paypal and get a response

        $viewResponse = Array();

        if(isset($ppResponse['L_ERRORCODE0']))
            $viewResponse['error'] = "Error: {$ppResponse['L_LONGMESSAGE0']}";
        else if(isset($ppResponse['ACK']) && $ppResponse['ACK'] == ('Success' || 'SuccessWithWarning'))
            $viewResponse['success'] = "Your subscription has been processed.";

        echo json_encode($viewResponse);
        die;

    }

    public function getGateway() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $defaultData = array(
            'apiUsername' => $this->payPalApiLogin,
            'apiPassword' => $this->payPalApiPassword,
            'apiSignature' => $this->payPalSignature,
            'testMode' => $this->testMode,
            //'returnUrl' => $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/subscribe/?task=getExpressCheckout',
            //'cancelUrl' => $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/subscribe/?task=error',
            //'notifyUrl' => $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/subscribe/?task=notify'
            'returnUrl' => $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/subscribenew/?paypal=paid',
            'cancelUrl' => $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/subscribenew/?paypal=cancel',
            'notifyUrl' => $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/subscribenew/?paypal=notify'

        );
        return $defaultData;

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

