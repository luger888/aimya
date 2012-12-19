<?php

class Application_Model_PayNow
{

    public function payNow()
    {
        $buyNow = new Aimya_PayPal_Paypal();

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
        $buyNow->addVar('business','seller_1355909799_biz@gmail.com');	/* Payment Email */
        $buyNow->addVar('cmd','_xclick');
        $buyNow->addVar('amount','99.25');
        $buyNow->addVar('item_name','Script Support');
        $buyNow->addVar('item_number','PHPCLASS8');
        $buyNow->addVar('quantity','1');
        $buyNow->addVar('tax','1.99');
        $buyNow->addVar('shipping','8.00');
        $buyNow->addVar('currency_code','USD');
        $buyNow->addVar('no_shipping','2');		/* Must provide shipping address */
        $buyNow->addVar('rm','2');			/* Return method must be POST (2) for this class */
        /* Paypal IPN URL - MUST BE URL ENCODED */
        $buyNow->addVar('notify_url', 'http://aimya.svitla.com/en/test/response');
        /*
Thank you Page (if any) - not included in this package*/
        /*
        $buyNow->addVar('return','thanks.html');
        */

        /*
        Now add a button
        */
        $buyNow->addButton(1);	/* Default buy now button */
        /* or use custom buttons */
        /*
        $buyNow->addButton(6,'http://farm1.static.flickr.com/34/110260847_779dd141a6.jpg?v=0');
        */
        /* Show final form */
        $buyNow->showForm();

        /*
        To get the form in URL Form (when supported)
        You use:
        */
        echo '<a href="'.$buyNow->getLink().'">Click Here</a>';
    }

}

