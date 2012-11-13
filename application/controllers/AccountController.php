<?php
class AccountController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {
        $this->_helper->AjaxContext()
            ->addActionContext('edit', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->view->headScript()->appendFile('../../js/jquery/account/tabs/services.js');
        $this->view->headScript()->appendFile('../../js/jquery/account/tabs/users.js');
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
                if ($_FILES['avatar']['name']) { //if new avatar -> update db
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
            $dbServiceDetail = new Application_Model_DbTable_ServiceDetail();

            if ($this->getRequest()->getParam('saveService')) {

                $formData = $this->getRequest()->getPost();

                if ($servicesForm->isValid($formData)) {
                    $dbServiceDetail->addService($formData, $identity->id);
                    $this->_helper->redirector('index', 'account');
                }

            } else if ($this->getRequest()->getParam('updateService')) {
                $updateData = $this->getRequest()->getPost();
                if ($servicesForm->isValid($updateData)) {
                    $dbServiceDetail->updateService($updateData, $identity->id);
                    $this->_helper->redirector('index', 'account');
                }
            }

        }

        $this->view->services = $servicesModel->getServiceByUser($identity->id);
        $this->view->servicesForm = $servicesForm;
    }

    public function availabilityAction()
    {
        $this->_helper->layout()->disableLayout();
        $dbAvailability = new Application_Model_DbTable_Availability();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $availabilityForm = new Application_Form_Availability();
        $this->view->availabilityForm = $availabilityForm->populate($dbAvailability->getAvailability($identity->id));
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($availabilityForm->isValid($formData)) {
                $dbAvailability->updateAvailability($formData, $identity->id);

                $this->_helper->redirector('index', 'account');


            } else {

                $this->view->errors = $availabilityForm->getErrors();

            }
        }
    }

    public function notificationsAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $dbNotifications = new Application_Model_DbTable_Notifications();
        $notificationForm = new Application_Form_Notifications();
        $this->view->notificationsForm = $notificationForm->populate($dbNotifications->getNotifications($identity->id));
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($notificationForm->isValid($formData)) {

                $dbNotifications->updateNotifications($formData, $identity->id);
                $this->_helper->redirector('index', 'account');

            } else {

                $this->view->errors = $notificationForm->getErrors();

            }
        }
    }

    public function usersAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $profileModel = new Application_Model_Profile();
        $this->view->friends = $profileModel->getFriends($identity->id);

    }

    public function resumeAction()
    {
        $this->_helper->layout()->disableLayout();

        $identity = Zend_Auth::getInstance()->getStorage()->read();

        $servicesModel = new Application_Model_DbTable_ServiceDetail();
        $this->view->services = $servicesModel->getServiceByUser($identity->id);

        $profileModel = new Application_Model_Profile();
        $this->view->profile = $profileModel->getProfileAccount($identity->id);
        $this->view->avatarPath = $profileModel->getAvatarPath($identity->id); //path to avatar
    }

    public function metricsAction()
    {
        $this->_helper->layout()->disableLayout();

    }

    public function editAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        if ($this->getRequest()->isPost()) {
            $dbServiceDetail = new Application_Model_DbTable_ServiceDetail();
            $dbUserRelations = new Application_Model_DbTable_UserRelations();
            /*Service Details tab*/
            if ($this->getRequest()->getParam('deleteService')) {

                $dbServiceDetail->deleteService($this->getRequest()->getParam('deleteService'), $identity->id);

            } else if ($this->getRequest()->getParam('editService')) {
                $this->view->editForm = $dbServiceDetail->getItem($this->getRequest()->getParam('editService'));

            }
            /*  Users tab, update relations    */
            if($this->getRequest()->getParam('updateUserId')){
                $dbUserRelations->updateUserStatus($this->getRequest()->getPost(), $identity->id);
            }

        }
    }

    public function featuresAction()
    {
        $this->view->headScript()->appendFile('../../js/jquery/account/features.js');
        $dbUserModel = new Application_Model_DbTable_Users();
        $this->view->filters = new Application_Form_FeaturesFilter();

        if ($this->getRequest()->isGet()) {
            $userType = $this->getRequest()->getParam('user');// Teacher or students only
            $lessonCat = $this->getRequest()->getParam('category');

            $this->view->featured = $dbUserModel->getLatestFeatured($userType, $lessonCat);
        }else{
            $this->view->featured = $dbUserModel->getLatestFeatured();
        }

    }

}