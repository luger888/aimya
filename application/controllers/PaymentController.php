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
        $payPalModel = new Application_Model_PayPal();
        $response = $payPalModel->generateRequestXml(54, 127);

        if($response) {
            echo $response;
            die;
        } else {

        }

    }

}