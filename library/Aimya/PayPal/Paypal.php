<?php

class Aimya_PayPal_Paypal
{
    private $VARS;
    private $button;
    private $logFile;
    private $isTest=true;

    /* Print Form as Link */
    function getLink()
    {
        $url = $this->getPaypal();
        $link = 'https://'.$url.'/cgi-bin/webscr?';
        foreach($this->VARS as $item => $sub){
            $link .= $sub[0].'='.$sub[1].'&';
        }
        return $link;
    }

    /* Print Form */
    function showForm()
    {
        $url = $this->getPaypal();
        $FORM  = '<form action="https://'.$url.'/cgi-bin/webscr" method="post" target="_blank" style="display:inline;">'."\n";

        foreach($this->VARS as $item => $sub){
            $FORM .= '<input type="hidden" name="'.$sub[0].'" value="'.$sub[1].'">'."\n";
        }

        $FORM .= $this->button;
        $FORM .= '</form>';
        echo $FORM;
    }

    /* Add variable to form */
    function addVar($varName,$value)
    {
        $this->VARS[${'varName'}][0] = $varName;
        $this->VARS[${'varName'}][1] = $value;
    }

    /* Add button Image */
    function addButton($type,$image = NULL)
    {
        switch($type)
        {
            /* Buy now */
            case 1:
                $this->button = '<input type="image" height="21" style="width:86;border:0px;"';
                $this->button .= 'src="https://www.paypal.com/en_US/i/btn/btn_paynow_SM.gif" border="0" name="submit" ';
                $this->button .= 'alt="PayPal - The safer, easier way to pay online!">';
                break;
            /* Add to cart */
            case 2:
                $this->button = '<input type="image" height="26" style="width:120;border:0px;"';
                $this->button .= 'src="https://www.paypal.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit"';
                $this->button .= 'alt="PayPal - The safer, easier way to pay online!">';
                break;
            /* Donate */
            case 3:
                $this->button = '<input type="image" height="47" style="width:122;border:0px;"';
                $this->button .= 'src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit"';
                $this->button .= 'alt="PayPal - The safer, easier way to pay online!">';
                break;
            /* Gift Certificate */
            case 4:
                $this->button = '<input type="image" height="47" style="width:179;border:0px;"';
                $this->button .= 'src="https://www.paypal.com/en_US/i/btn/btn_giftCC_LG.gif" border="0" name="submit"';
                $this->button .= 'alt="PayPal - The safer, easier way to pay online!">';
                break;
            /* Subscribe */
            case 5:
                $this->button = '<input type="image" height="47" style="width:122;border:0px;"';
                $this->button .= 'src="https://www.paypal.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit"';
                $this->button .= 'alt="PayPal - The safer, easier way to pay online!">';
                break;
            /* Custom Button */
            default:
                $this->button = '<input type="image" src="'.$image.'" border="0" name="submit"';
                $this->button .= 'alt="PayPal - The safer, easier way to pay online!">';
        }
        $this->button .= "\n";
    }

    /* Set log file for invalid requests */
    function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }

    /* Helper function to actually write to logfile */

    private function doLog($data)
    {

        /*ob_start();
        echo '<pre>'; print_r($_POST); echo '</pre>';
        $logInfo = ob_get_contents();
        ob_end_clean();

        $file = fopen($this->logFile,'a');
        fwrite($file,$logInfo);
        fclose($file);*/

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


    /* Check payment */
    function checkPayment($data = array())
    {

        /* read the post from PayPal system and add 'cmd' */
//        $req = 'cmd=_notify-validate';
//
//        /* Get post values and store them in req */
//        foreach ($data as $key => $value) {
//            $value = urlencode(stripslashes($value));
//            $req .= "&$key=$value";
//        }
        $validate_request = 'cmd=_notify-validate';
        foreach ($data as $key => $value)
        {
            $validate_request .= "&" . $key . "=" . urlencode(stripslashes($value));
        }
        $url = $this->getPaypal();
        // Validate PayPal data to ensure it actually originated from PayPal
        $curl_result = '';
        $curl_err = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $validate_request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($validate_request)));
        curl_setopt($ch, CURLOPT_HEADER , 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $curl_result = curl_exec($ch);
        $curl_err = curl_error($ch);


        curl_close($ch);
        /* post back to PayPal system to validate */
//        $header = "";
//        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
//        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
//        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
//        $fp = fsockopen ('ssl://'.$url, 443, $errno, $errstr, 30);

        /*
          If ssl access gives you problem. try regular port:
          $fp = fsockopen ($url, 80, $errno, $errstr, 30);
          */
        if (strpos($curl_result, "VERIFIED")!==false) {
            $this->doLog($curl_result);
            return true;
        }
        elseif(strpos($curl_result, "INVALID")!==false) {
            $this->doLog("bla bla bla");
            $this->doLog($curl_result);
            return false;
        } else {
            $this->doLog("lab lab lab");
            $this->doLog($curl_result);
            return false;
        }

//        if (!$fp) {
//            /* HTTP ERROR */
//            return false;
//        } else {
//            fputs ($fp, $header . $req);
//            while (!feof($fp)) {
//                $res = fgets ($fp, 1024);
//                if (strcmp ($res, "VERIFIED") == 0) {
//                    /*
//                         check the payment_status is Completed
//                         check that txn_id has not been previously processed
//                         check that receiver_email is your Primary PayPal email
//                         check that payment_amount/payment_currency are correct
//                         process payment
//                         */
//                    return true;
//                } else {
//                    				if (strcmp ($res, "INVALID") == 0) {
//                    /*
//                         log for manual investigation
//                         */
//                    if($this->logFile != NULL){
//
//                        $this->doLog($data);
//                    }
//                }
//
//                    return false;
//                }
//            }
//            fclose ($fp);
//        }
//        return false;
    }

    /* Set Test */
    function useSandBox($value)
    {
        $this->isTest=$value;
    }

    /* Private function to get paypal url */
    private function getPaypal()
    {
        if($this->isTest == true){
            return 'www.sandbox.paypal.com';
        } else {
            return 'www.paypal.com';
        }
    }

}