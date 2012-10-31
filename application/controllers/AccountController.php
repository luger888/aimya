<?php
class AccountController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {

    }

    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $profileForm = new Application_Form_Profile();
        $profileModel = new Application_Model_DbTable_Profile();


        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();


            if ($profileForm->isValid($formData)) {
                $profileForm->avatar->receive;

                $updateProfile = new Application_Model_DbTable_Profile();
                $updateProfile->updateProfile($formData, $identity->id);

            } else {

                $this->view->errors = $profileForm->getErrors();

            }
        }else{

            $this->view->profile = $profileForm->populate($profileModel->getProfile($identity->id));$this->view->profile = $profileForm->populate($profileModel->getProfile($identity->id));
        }
    }

}