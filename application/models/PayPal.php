<?php

class Application_Model_PayPal
{
    private $videoCost = 3;
    private $notesCost = 2;
    private $feedbackCost = 2;

    private $payPalApiLogin = 'saaant_1294144318_biz_api1.mail.ru';
    private $payPalApiPassword = '1294144327';
    private $payPalSignature = 'Abnyp.Z2zyY-WdA4Tu7.O0nvLTCOAOWNAuJ8OQHrTwCU9KUpnW2voS4v';
    private $payPalApiId = 'APP-80W284485P519543T';
    private $adaptivUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Pay';
    private $amiyaPayPalEmail = 'saaant_1294144318_biz@mail.ru';

    public function generateXml($sellerId, $bookingId) {

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $cancelUrl = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/lesson/index';
        $returnURL = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/lesson/index';
        $ipnURL = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl() . '/payment/ipn';

        $this->writeLog($ipnURL);

        $profileTable = new Application_Model_DbTable_Profile();
        $bookingTable = new Application_Model_DbTable_Booking();

        $paypalEmail = $profileTable->getPayPalEmail($sellerId);
        $paypalEmail['paypal_email'] = 'seller_1355909799_biz@gmail.com';

        $booking = $bookingTable->getItem($bookingId);

        $rate = $booking['rate'];
        $userProfit = 0;
        $aimyaProfit = 0;
        if($booking['video'] == 1) {
            $userProfit += $rate - $this->videoCost;
            $aimyaProfit += $this->videoCost;
        }
        if($booking['notes'] == 1) {
            $userProfit += $rate - $this->notesCost;
            $aimyaProfit += $this->notesCost;
        }
        if($booking['feedback'] == 1) {
            $userProfit += $rate - $this->feedbackCost;
            $aimyaProfit += $this->feedbackCost;
        }


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
        $body_data .= "<invoiceID>{$booking['id']}</invoiceID>";
        $body_data .= "</receiver>";
        $body_data .= "<receiver>";
        $body_data .= "<amount>{$aimyaProfit}</amount>";
        $body_data .= "<email>{$this->amiyaPayPalEmail}</email>";
        $body_data .= "<InvoiceID>{$booking['id']}</InvoiceID>";
        $body_data .= "</receiver>";
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
            return "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $paykey;
        } else {
            return false;
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

