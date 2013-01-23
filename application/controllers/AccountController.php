<?php
class AccountController extends Zend_Controller_Action implements Aimya_Controller_AccountInterface
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('edit', 'json')
            ->addActionContext('offline', 'json')
            ->addActionContext('online', 'json')
            ->addActionContext('features', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->view->role = $identity->role;
        $this->view->headScript()->appendFile('../../../js/jquery/account/tabs/services.js');
        $this->view->headScript()->appendFile('../../../js/jquery/account/tabs/users.js');
        $profileForm = new Application_Form_Profile();
        $profileModel = new Application_Model_Profile();
        $this->view->profile = $profileForm->populate($profileModel->getProfileAccount($identity->id));
        $this->view->avatarPath = $profileModel->getAvatarPath($identity->id, 'medium'); //path to avatar
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

                $timezoneTable = new Application_Model_DbTable_TimeZones();
                $timezone = $timezoneTable->getItem($formData['timezone']);
                $formData['timezone'] = $timezone['gmt'];
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

            }


        }

        $this->view->services = $servicesModel->getServiceByUser($identity->id, 1);
        $this->view->servicesForm = $servicesForm;
    }

    public function requestservicesAction()
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

            }


        }

        $this->view->services = $servicesModel->getServiceByUser($identity->id, 2);
        $this->view->servicesForm = $servicesForm;
    }

    public function availabilityAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $dbAvailability = new Application_Model_DbTable_Availability();
        $dbUser = new Application_Model_DbTable_Users();
        $this->view->user = $dbUser->getUser($identity->id);

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


            if($this->getRequest()->getParam('newPassword') !=''){

            }

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
        $this->view->services = $servicesModel->getServiceByUser($identity->id, 1);

        $profileModel = new Application_Model_Profile();
        $this->view->profile = $profileModel->getProfileAccount($identity->id);
        $this->view->avatarPath = $profileModel->getAvatarPath($identity->id); //path to avatar
    }

    public function metricsAction()
    {
        $this->_helper->layout()->disableLayout();

        $friendsDb = new Application_Model_DbTable_Friends();
        $ordersDb = new Application_Model_DbTable_Orders();

        $totalPeersCount = $friendsDb->getJoinedPeers();
        $monthPeersCount = $friendsDb->getJoinedPeers('month');
        $this->view->totalPeersCount = $totalPeersCount['peers_count'];
        $this->view->monthPeersCount = $monthPeersCount['peers_count'];

        $totalIncome = $ordersDb->getUserIncome();
        $monthIncome = $ordersDb->getUserIncome('month');
        $yearIncome = $ordersDb->getUserIncome('year');
        $this->view->totalIncome = $totalIncome['user_profit'];
        $this->view->yearIncome = $yearIncome['user_profit'];
        $this->view->monthIncome = $monthIncome['user_profit'];

    }

    public function editAction()
    {
        $this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        if ($this->getRequest()->isPost()) {
            $dbServiceDetail = new Application_Model_DbTable_ServiceDetail();
            $dbUserRelations = new Application_Model_DbTable_Friends();
            /*Service Details tab*/
            if ($this->getRequest()->getParam('deleteService')) {

                $dbServiceDetail->deleteService($this->getRequest()->getParam('deleteService'), $identity->id);

            } else if ($this->getRequest()->getParam('getServiceCategories')) {
                $servicesForm = new Application_Form_ServiceDetails();
                $this->view->servicesForm = $servicesForm;
                $lessonDbModel = new Application_Model_DbTable_LessonCategory();
                $durationDbModel = new Application_Model_DbTable_LessonDuration();
                $this->view->categories = $lessonDbModel->getLessonCategories();
                $this->view->durations = $durationDbModel->getLessonDurations();
            }
            /*  Users tab, update relations    */
            if ($this->getRequest()->getParam('updateUserId')) {
                $dbUserRelations->updateUserStatus($this->getRequest()->getPost(), $identity->id);
            }
            if ($this->getRequest()->getParam('updateService')) {

                $dbServiceDetail->updateService($this->getRequest()->getPost(), $identity->id);

            }
            if ($this->getRequest()->getParam('deleteAvatar')) {

                $dbProfile = new Application_Model_DbTable_Profile();
                $profileModel = new Application_Model_Profile();
                unlink(substr($profileModel->getAvatarPath($identity->id, 'base'), 1)); //substr first slah
                unlink(substr($profileModel->getAvatarPath($identity->id, 'medium'), 1));

                $dbProfile->deleteAvatar($identity->id);


            }


        }
    }

    public function featuresAction()
    {
        $this->view->headScript()->appendFile('../../js/jquery/account/features.js');
        $dbUserModel = new Application_Model_DbTable_Users();

        $userType = '0';
        if ($this->getRequest()->getParam('user')) {
            $userType = $this->getRequest()->getParam('user');
        }
        $lessonCat = 'All';
        if ($this->getRequest()->getParam('category')) {
            $lessonCat = $this->getRequest()->getParam('category');
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $offset = $this->getRequest()->getParam('offset');
            $count = $this->getRequest()->getParam('count');
            $profileModel = new Application_Model_Profile();
            $featuredHtml = "";
            $users = $dbUserModel->getLatestFeatured($userType, $lessonCat, $offset, $count);
            foreach ($users as $person) {
                $friendTable = new Application_Model_DbTable_Friends();
                $isFriend = $friendTable->isFriend($person['id']);
                $isPending = $friendTable->isPending($person['id']);
                $avatarPath = $profileModel->getAvatarPath($person['id'], 'medium');
                $featuredHtml .= "
                    <div class='shadowSeparator clearfix'>
                        <div class='shadowSeparatorBox clearfix'>
                            <div class='featureItem clearfix'>
                                <div class='imageBlock boxShadow'><img src='" . $avatarPath . "'></div>
                                <div class='featuredButtonsTop clearfix'>
                                    <a class ='button-2 view viewProfile' href='" . Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/' . $person['id'] . "'>" . $this->view->translate('VIEW PROFILE') . "</a>";
                if (Zend_Auth::getInstance()->getIdentity()->id != $person['id']) {
                    if ($isFriend) {
                        $featuredHtml .= "<a class ='sendMessage' onclick='sendMessage(" . $person['id'] . ", this);' href='javascript:void(1)'>" . $this->view->translate('SEND MESSAGE') . "</a>";
                    } elseif ($isPending) {
                        $featuredHtml .= "<span class ='request_sent'>" . $this->view->translate('REQUEST SENT') . "</span>";
                    } else {
                        $featuredHtml .= "<a class ='button-2 add addAccount' onclick='addToFriend(" . $person['id'] . ", this);' href='javascript:void(1)'>" . $this->view->translate('ADD TO MY ACCOUNT') . "</a>";
                    }
                }
                $featuredHtml .= "<input type='hidden' value='" . $person['id'] . "'>
                                </div>
                                <ul class='featuredInfo'>
                                    <li class='clearfix'>
                                        <span class='title'>" . $this->view->translate('Name:') . "</span>
                                        <div class='featuredTxt'>" . ' ' . $person['firstname'] . ' ' . $person['lastname'] . "</div>
                                    </li>
                                    <li class='clearfix'>
                                        <span class='title'>" . $this->view->translate('Time zone:') . "</span>
                                        <div class='featuredTxt'>" . ' UTC ' . $person['timezone'] . "</div>
                                    </li>";

                if (count($person['service_offered']) > 0) {
                    $featuredHtml .= "
                                            <li class='clearfix'>
                                            <span class='title'>" . $this->view->translate('Service offered:') . "</span>
                                                <div class='featuredTxt'>";
                    $i = 0;
                    foreach ($person['service_offered'] as $index => $service) {
                        $featuredHtml .= $service['subcategory'];
                        if (count($person['service_offered']) - 1 != $i) $featuredHtml .= ', ';
                        $i++;
                    }
                    $featuredHtml .= "
                                                </div>
                                            </li>";
                }
                if (count($person['service_requested']) > 0) {
                    $featuredHtml .= "
                                            <li class='clearfix'>
                                                <span class='title'>" . $this->view->translate('Service requested:') . "</span>
                                                <div class='featuredTxt'>";
                    $i = 0;
                    foreach ($person['service_requested'] as $index => $service) {
                        $featuredHtml .= $service['subcategory'];
                        if (count($person['service_requested']) - 1 != $i) $featuredHtml .= ', ';
                        $i++;
                    }
                    $featuredHtml .= "
                                                </div>
                                            </li>";
                }
                if (count($person['add_info']) > 0) {
                    $featuredHtml .= "
                                            <li class='clearfix'>
                                                <span class='title'>" . $this->view->translate('About me:') . " </span>
                                                <div class='featuredTxt more'><" . $person['add_info'] . "</div>
                                            </li>";
                }
                $featuredHtml .= "
                                </ul>
                            </div>
                        </div>
                    </div>";
            }

            $this->view->featuredHtml = $featuredHtml;

        } else {
            $featuredList = $dbUserModel->getLatestFeatured($userType, $lessonCat);
            $featuredCount = $dbUserModel->getFeaturedCount($userType, $lessonCat);

            $this->view->filters = new Application_Form_FeaturesFilter();
            $this->view->featured = $featuredList;
            $this->view->featuredCount = $featuredCount;
        }


    }

    public function offlineAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $onlineUserTable = new Application_Model_DbTable_OnlineUsers();
        $status = $onlineUserTable->makeOffline($userId);
        $this->view->userStatus = $status;
    }

    public function onlineAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $onlineUserTable = new Application_Model_DbTable_OnlineUsers();
        $res = $onlineUserTable->makeOnline($userId);
        if ($res) {
            $this->view->answer = 'success';
        } else {
            $this->view->answer = 'error';
            $this->view->data = 'problem with mysql request';
        }
    }

}