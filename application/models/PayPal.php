<?php

class Application_Model_PayPal
{

    public function generateRequestXml($sellerId, $bookingId) {

        $cancelUrl = "http://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/lesson/index';
        $returnURL = "http://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/lesson/index';
        $ipnURL = "http://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/test/responsenew';
        $adaptivUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Pay';

        $payPalApiLogin = 'saaant_1294144318_biz_api1.mail.ru';
        $payPalApiPassword = '1294144327';
        $payPalSignature = 'Abnyp.Z2zyY-WdA4Tu7.O0nvLTCOAOWNAuJ8OQHrTwCU9KUpnW2voS4v';
        $payPalApiId = 'APP-80W284485P519543T';


        $userTable = new Application_Model_DbTable_Users();
        $bookingTable = new Application_Model_DbTable_Booking();

        $seller = $userTable->getItem($sellerId);
        $booking = $bookingTable->getItem($bookingId);

        $body_data  = "<?xml version='1.0'?>";
        $body_data .= " <payRequest>";
        $body_data .= " <actionType>PAY</actionType>";
        $body_data .= " <cancelUrl>{$cancelUrl}</cancelUrl>";
        $body_data .= " <returnUrl>{$returnURL}</returnUrl>";
        $body_data .= " <currencyCode>USD</currencyCode>";
        $body_data .= " <receiverList>";
        $body_data .= "  <receiver>";
        $body_data .= "   <amount>3</amount>";
        $body_data .= "   <email>seller_1355909799_biz@gmail.com</email>";
        $body_data .= "  </receiver>";
        $body_data .= "  <receiver>";
        $body_data .= "   <amount>5</amount>";
        $body_data .= "   <email>saaant_1294144318_biz@mail.ru</email>";
        $body_data .= "  </receiver>";
        $body_data .= " </receiverList>";
        $body_data .= " <requestEnvelope>";
        $body_data .= "  <errorLanguage>en_US</errorLanguage>";
        $body_data .= " </requestEnvelope>";
        $body_data .= "<ipnNotificationUrl>{$ipnURL}</ipnNotificationUrl>";
        $body_data .= "</payRequest>";

        $params = array(
            "http" => array(
                "method" => "POST",
                "content" => $body_data,

                "header" => "Content-type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen ( $body_data ) . "\r\n" .
                    "X-PAYPAL-SECURITY-USERID: {$payPalApiLogin}\r\n" .
                    "X-PAYPAL-SECURITY-SIGNATURE: {$payPalSignature}\r\n" .
                    "X-PAYPAL-SECURITY-PASSWORD: {$payPalApiPassword}\r\n" .
                    "X-PAYPAL-APPLICATION-ID: {$payPalApiId}\r\n" .
                    "X-PAYPAL-REQUEST-DATA-FORMAT: XML\r\n" .
                    "X-PAYPAL-RESPONSE-DATA-FORMAT: XML\r\n"
            )
        );
        $ctx = stream_context_create($params);
        $fp = fopen($adaptivUrl, "r", false, $ctx);

        $response = stream_get_contents($fp);

        $xmlresponse = simplexml_load_string($response);
        $ack = trim($xmlresponse->responseEnvelope->ack) ;
        $paykey = trim($xmlresponse->payKey);

        fclose($fp);

        if ($ack === 'Success') {
            return "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $paykey;
        } else {
            return false;
        }

    }

}

