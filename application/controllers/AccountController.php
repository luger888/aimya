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
            ->addActionContext('updateavailability', 'json')
            ->addActionContext('changepassword', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        //basic tab
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $this->view->role = $identity->role;
        $this->view->headScript()->prependFile('/js/jquery/account/tabs/services.js');
        $this->view->headScript()->prependFile('/js/jquery/account/tabs/users.js');
        $timezoneTable = new Application_Model_DbTable_TimeZones();
        $profileForm = new Application_Form_Profile();
        $profileModel = new Application_Model_Profile();


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($profileForm->isValid($formData)) {
                $updateUser = new Application_Model_DbTable_Users();
                $currentUser = $updateUser->getItem($identity->id);
                $checkByUsername = $updateUser->checkByUsername($formData['username']);

                if ($checkByUsername && $currentUser['username'] != $formData['username']) {
                    $this->_helper->flashMessenger->addMessage(array('failure' => 'This username already exist, please select another username'));
                    $formData['username'] = $checkByUsername['username'];
                    $this->_helper->redirector('index', 'account');
                }
                $checkByEmail = $updateUser->checkByMail($formData['email']);
                if ($checkByEmail && $currentUser['email'] != $formData['email']) {
                    $this->_helper->flashMessenger->addMessage(array('failure' => 'This email already exist, please select another email'));
                    $formData['email'] = $checkByEmail['email'];
                    $this->_helper->redirector('index', 'account');
                }

                Zend_Registry::set('username', "{$formData['firstname']} {$formData['lastname']}");
                $profileForm->avatar->receive();

                $updateProfile = new Application_Model_DbTable_Profile();
                $updateProfile->updateProfile($formData, $identity->id);
                if ($_FILES['avatar']['name']) { //if new avatar -> update db
                    $updateProfile->updateAvatar($_FILES['avatar']['name'], $identity->id);
                }

                $timezone = $timezoneTable->getItem($formData['timezone']);
                $formData['timezone'] = $timezone['id'];
                $updateUser->updateUser($formData, $identity->id);
            } else {
                $this->view->errors = $profileForm->getErrors();

            }
        }
        $accountData = $profileModel->getProfileAccount($identity->id);
        if($accountData['timezone'] != NULL) {
            $timezoneId = $timezoneTable->getItem($accountData['timezone']);
            $accountData['timezone'] = $timezoneId['id'];
        }



        $this->view->profile = $profileForm->populate($accountData);
        $this->view->avatarPath = $profileModel->getAvatarPath($identity->id, 'medium'); //path to avatar
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

    public function updateavailabilityAction()
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
                $this->view->success = 1;
            } else {

                $this->view->errors = $availabilityForm->getErrors();
                $this->view->success = 0;
            }
        }
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
        $usersDb = new Application_Model_DbTable_Users();
        $this->view->notificationsForm = $notificationForm->populate($dbNotifications->getNotifications($identity->id));

        if ($this->getRequest()->isPost()) {


            $formData = $this->getRequest()->getPost();

            if ($notificationForm->isValid($formData)) {

                $dbNotifications->updateNotifications($formData, $identity->id);

                $this->_helper->redirector('index', 'account');

            } else {

                $this->view->errors = $notificationForm->getErrors();
                $this->_helper->redirector('index', 'account');

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
                if ($this->getRequest()->getParam('status') != 3) {
                    $dbUserRelations->updateUserStatus($this->getRequest()->getPost(), $identity->id);
                } else {
                    $dbUserRelations->deleteFriend($this->getRequest()->getPost());
                }

            }
            if ($this->getRequest()->getParam('updateService')) {

                $dbServiceDetail->updateService($this->getRequest()->getPost(), $identity->id);

            }
            if ($this->getRequest()->getParam('deleteAvatar')) {

                $dbProfile = new Application_Model_DbTable_Profile();
                $profileModel = new Application_Model_Profile();
                if (substr($profileModel->getAvatarPath($identity->id, 'base'), 0, 12) != '/img/design/') {
                    unlink(substr($profileModel->getAvatarPath($identity->id, 'base'), 1)); //substr first slah
                    unlink(substr($profileModel->getAvatarPath($identity->id, 'medium'), 1));

                }


                $dbProfile->deleteAvatar($identity->id);


            }


        }
    }

    public function changepasswordAction()
    {
        if ($this->getRequest()->isPost()) {
            $identity = Zend_Auth::getInstance()->getStorage()->read();
            $usersDb = new Application_Model_DbTable_Users();
            $userInfo = $usersDb->getUserInfo($identity->id);
            $userpassword = $userInfo['password'];
            if ($this->getRequest()->getParam('newPassword') != '') {
                //validators
                $stringLengthValidator = new Zend_Validate_StringLength(6, 20); //addValidator('stringLength', false, array(6, 200));
                $regexValidator = new Zend_Validate_Regex(array(
                    'pattern' => '/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/',
                    'messages' => array(
                        'regexNotMatch' => "Your password must contain letters and numbers.",
                    )
                ));

                if (!$stringLengthValidator->isValid($this->getRequest()->getParam('newPassword'))) {
                    $this->view->errorLength = 1;
                } else if (!$regexValidator->isValid($this->getRequest()->getParam('newPassword'))) {
                    $this->view->errorReg = 1;
                }else if ($this->getRequest()->getParam('newPassword') == $this->getRequest()->getParam('oldPassword')) {
                    $this->view->errorSame = 1;
                }
                else {


                    $password = md5($this->getRequest()->getParam('oldPassword'));

                    if ($userpassword==$password) {

                        $usersDb->changePass($this->getRequest()->getParam('newPassword'), $identity->id);
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Password successfully changed'));
                        $this->view->success = 1;

                    } else {
                        $this->_helper->flashMessenger->addMessage(array('failure' => 'Error with changing password, please try again later'));
                        $this->view->passError = 'Wrong password!';
                    }
                }
            }
        }
    }

    public function featuresAction()
    {
        $this->view->headScript()->prependFile('/js/jquery/account/features.js');
        $dbUserModel = new Application_Model_DbTable_Users();
        $userType = 0;
        if ($this->getRequest()->getParam('user')) {
            $userType = (int)$this->getRequest()->getParam('user');
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
                $reviewModel = new Application_Model_Review();
                $reviewAv = $reviewModel->getAverageReview($person['id']);
                $friendTable = new Application_Model_DbTable_Friends();
                $isFriend = $friendTable->isFriend($person['id']);
                $isPending = $friendTable->isPending($person['id']);
                $isInList = $friendTable->isInList($person['id']);
                $userModel = new Application_Model_DbTable_Users();
                $timeZone = $userModel->getTimeZone();
                $tzDbTable = new Application_Model_DbTable_TimeZones();
                $timeZone =  $tzDbTable->getItem($timeZone['timezone']);
                $person['timezone'] = $timeZone['gmt'];
                $defaultRequestText = '';
                $avatarPath = $profileModel->getAvatarPath($person['id'], 'medium');
                 $role = ($person['role'] == '1' ? 'Student' : 'Instructor');
                $featuredHtml .= "
                    <div class='shadowSeparator clearfix'>
                        <div class='shadowSeparatorBox clearfix'>
                            <div class='featureItem clearfix'>
                            <div class ='leftBlockFeature'><div class='imageBlock boxShadow'><img src=".$avatarPath."></div><div class='profileRole'>
                ".$role."</div>
                <div class='starsBlock'>
                    <span data-rating='4' class='stars'></span>
                </div>
                        </div>
                                <div class='featuredButtonsTop clearfix'>
                                    <a class ='button-2 view viewProfile' href='" . Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/' . $person['id'] . "'>" . $this->view->translate('VIEW PROFILE') . "</a>";
                if (Zend_Auth::getInstance()->getIdentity()->id != $person['id']) {
                    if ($isFriend) {
                        $featuredHtml .= "<a class ='sendMessage createMessage button-2 floatLeft' onclick='sendMessage(" . $person['id'] . ", this);' href='javascript:void(1)'>" . $this->view->translate('SEND MESSAGE') . "</a>";
                    } elseif ($isPending) {
                        $featuredHtml .= "<span class ='request_sent'>" . $this->view->translate('REQUEST SENT') . "</span>";
                    } else {
                        if ($isInList['friend_id'] == Zend_Auth::getInstance()->getIdentity()->id && $isInList['recipient_status'] == 0) {
                            $defaultRequestText = "Hello  {$person['username']}, I have approved your request.";
                            $myText = "showFriendFormFeatured({$person['id']}, \"$defaultRequestText\", this)";
                            $featuredHtml .= "<a class='button-2 add addAccount' onclick='$myText' href='javascript:void(1)'>" . $this->view->translate('ADD TO MY ACCOUNT') . "</a>";
                        } else {
                            $defaultRequestText = "Hello  {$person['username']}, I would like add you to my account.";
                            $myText = "showFriendFormFeatured({$person['id']}, \"$defaultRequestText\", this)";
                            $featuredHtml .= "<a class='button-2 add addAccount' onclick='$myText' href='javascript:void(1)'>" . $this->view->translate('ADD TO MY ACCOUNT') . "</a>";
                        }
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
                                                <div class='featuredTxt more'>" . $person['add_info'] . "</div>
                                            </li>";
                }
                $featuredHtml .= "
                                </ul>
                                </div>
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

    //curl
    public function curlcheckactivityAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $onlineUserTable = new Application_Model_DbTable_OnlineUsers();
        $onlineUserTable->curlIsOnline();
    }
}