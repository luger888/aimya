<?php

class Application_Model_PayNow
{

    public function payNow()
    {
        $doSubscribe = new Aimya_PayPal_Paypal();

        /*
        Do you want to test payments
        before you actually make a real
        payment? If So use the test mode:


        $buyNow->useSandBox(true);


        You will also need to create a sandbox account
        https://developer.paypal.com/
        and then create a test account to which you will
        use when making the test payments
        */

        /*
        Add variables to Form
        PARAMTERS MUST ADHERE TO PAYPAL STANDARDS
        View all paramters @ PaypalVariables.html
        located in main folder of this class
        */
        $doSubscribe->addVar('business', 'itnnetwork@gmail.com'); /* Payment Email */
        $doSubscribe->addVar('cmd', '_xclick-subscriptions');
        $doSubscribe->addVar('currency_code', 'USD');
        $doSubscribe->addVar('item_name', 'Script Support');
        $doSubscribe->addVar('item_number', 'PHPCLASS8');

        /* Set Free Trials */
        $doSubscribe->addVar('a1', '15'); /* 0 Cost */
        $doSubscribe->addVar('p1', '1'); /* For 30 */
        $doSubscribe->addVar('t1', 'M'); /* Days */
        /* Allowed t's: D -> Days, W -> Weeks , M -> Months, Y -> Years */

        /* a2 can also be another trial period */

        /* Regular Subscription Rates */
        $doSubscribe->addVar('a3', '99'); /* 0 Cost */
        $doSubscribe->addVar('p3', '1'); /* For 1 */
        $doSubscribe->addVar('t3', 'M'); /* Year */

        /* Unlimited recurring payments (till cancelled) */
        $doSubscribe->addVar('src', '1');
        /* Try payment again if it ever fails */
        $doSubscribe->addVar('sr1', '1');

        /* No note, required */
        $doSubscribe->addVar('no_note', '1');

        $doSubscribe->addVar('rm', '2'); /* Return method must be POST (2) for this class */
        /* Paypal IPN URL - MUST BE URL ENCODED */
        $doSubscribe->addVar('notify_url', 'http://aimya.local/en/test/paypal/');
        $doSubscribe->addVar('cancel_return', 'http://aimya.local/en/test/paypal/');
        /*
Thank you Page (if any) - not included in this package*/
        /*
        $doDonate->addVar('return','thanks.html');
        */

        /*
        Now add a button
        */
        $doSubscribe->addButton(5); /* Default subscription button */
        /* or use custom buttons */
        /*
        $doSubscribe->addButton(6,'http://farm3.static.flickr.com/2154/2173129258_2c40a673f5.jpg?v=0');
        */
        /* Show final form */
        $doSubscribe->showForm();

        /*
        To get the form in URL Form (when supported)
        You use:
        */
        echo '<a href="' . $doSubscribe->getLink() . '">Click Here</a>';
    }

}

