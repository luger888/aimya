<?php
class AccountController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {

    }

    public function indexAction()
    {
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $profileForm = new Application_Form_Profile();
        $profileModel = new Application_Model_Profile();
        $this->view->profile = $profileForm->populate($profileModel->getProfileAccount($identity->id));
        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();

            if ($profileForm->isValid($formData)) {
                Zend_Registry::set('username', "{$formData['firstname']} {$formData['lastname']}");
                $profileForm->avatar->receive();


                $updateProfile = new Application_Model_DbTable_Profile();
                $updateProfile->updateProfile($formData, $identity->id);
                if($_FILES['avatar']['name']){//if new avatar -> update db
                    $updateProfile->updateAvatar($_FILES['avatar']['name'], $identity->id);
                 }
                $updateUser = new Application_Model_DbTable_Users();
                $updateUser->updateUser($formData, $identity->id);

            } else {

                $this->view->errors = $profileForm->getErrors();

            }
        }
    }
    public function servicesAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $servicesForm = new Application_Form_ServiceDetails();
        $servicesModel = new Application_Model_DbTable_ServiceDetail();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($servicesForm->isValid($formData)) {
                $addService = new Application_Model_DbTable_ServiceDetail();
                $addService->addService($formData, $identity->id);
                $params = array('tab' => '2');
                $this->_helper->redirector('index', 'account', 'default', $params);
            }
        }
        $this->view->services = $servicesModel->getServiceByUser($identity->id);
        $this->view->servicesForm = $servicesForm;
    }

    public function availabilityAction()
    {
        $this->_helper->layout()->disableLayout();

    }

    public function notificationsAction()
    {
        $this->_helper->layout()->disableLayout();

    }

    public function usersAction()
    {
        $this->_helper->layout()->disableLayout();

    }

    public function resumeAction()
    {
        $this->_helper->layout()->disableLayout();

    }

    public function metricsAction()
    {
        $this->_helper->layout()->disableLayout();

    }

}