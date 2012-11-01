<?php
class AccountController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {

    }

    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        //basic tab
        $profileForm = new Application_Form_Profile();
        $profileModel = new Application_Model_Profile();
        $this->view->profile = $profileForm->populate($profileModel->getProfileAccount($identity->id));
        //services tab
        $servicesForm = new Application_Form_ServiceDetails();
        $this->view->services = $servicesForm;
        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();

            if ($profileForm->isValid($formData)) {
                Zend_Registry::set('username', "{$formData['firstname']} {$formData['lastname']}");
                $profileForm->avatar->receive();
                echo $_FILES['avatar']['name'];
                //pushing avatar name to form array
                array_push($formData, $_FILES['avatar']['name']);
                $formData['avatar'] = $formData[0];
                unset($formData[0]);

                $updateProfile = new Application_Model_DbTable_Profile();
                $updateProfile->updateProfile($formData, $identity->id);

                $updateUser = new Application_Model_DbTable_Users();
                $updateUser->updateUser($formData, $identity->id);

            } else {

                $this->view->errors = $profileForm->getErrors();

            }
        }
    }

}