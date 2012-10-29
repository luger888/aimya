<?php

class Aimya_Controller_BaseController extends Zend_Controller_Action
{

    public function init()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $bootstrap = $this->getInvokeArg('bootstrap');

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            //if AJAX request - disable lauout rendering
            Zend_Controller_Action_HelperBroker::removeHelper('layouts');
        }
    }


}

