<?php

class AccountController extends Zend_Controller_Action #implements Application_Controller_AccountInterface
{

    public function init()
    {

    }

    public function indexAction()
    {

        $profile = new Application_Form_Profile();
        $this->view->profile = $profile;

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();


            if ($profile->isValid($formData)) {
                $profile->avatar->receive;
                $identity = Zend_Auth::getInstance()->getStorage()->read();
                $updateProfile = new Application_Model_DbTable_Profile();
                $updateProfile->updateProfile($formData, $identity->id);

            } else {

                $this->view->errors = $profile->getErrors();

            }
        }
    }

}