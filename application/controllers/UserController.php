<?php
class UserController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('registration', 'json')
            ->addActionContext('login', 'json')
            ->addActionContext('timezone', 'json')
            ->addActionContext('resend', 'json')
            ->addActionContext('fb', 'json')
            ->addActionContext('linked', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $accountId = $this->getRequest()->getParam('id');
        if ($accountId) {
            $reviewModel = new Application_Model_Review();
            $this->view->stars = $reviewModel->getTotalReviews($accountId);

            $userModel = new Application_Model_DbTable_Users();
            $user = $userModel->getFullData($accountId);
            if ($user['username'] && $user['status'] !=2) {
                $userData = $user;
            } else {
                $this->_helper->redirector('page404', 'error');
            }
            $profileModel = new Application_Model_Profile();
            $dbProfile = new Application_Model_DbTable_Profile();
            $this->view->profile = $dbProfile->getProfile($accountId);
            $dbAvailability = new Application_Model_DbTable_Availability();
            $this->view->availability = $dbAvailability->getAvailability($accountId);
            $this->view->avatarPath = $profileModel->getAvatarPath($accountId, 'base');
            $this->view->userData = $userData;
        } else {
            $this->_helper->redirector('page404', 'error');
        }

    }

    public function confirmationAction()
    {

        $data = $this->getRequest()->getParams();

        if (isset($data)) {
            if (!$data['query_id']) {
                $this->view->message = 'no email';
            } else if (!$data['query_key']) {
                $this->view->message = 'no token';
            } else if (!$data['query_key'] && !$data['query_id']) {

                $this->view->message = 'no data';

            } else {
                $model = new Application_Model_User();
                $this->view->message = $model->approveUser($data);

                $this->_helper->flashMessenger->addMessage(array('success' => 'Your account has been confirmed. Please login'));

                //$this->_helper->redirector('http://aimya.com');
                header('location:http://aimya.com');
            }
        } else {
            $this->view->message = 'request data is incorrect';
        }

    }

    public function recoveryAction()
    {
        $this->_helper->layout->setLayout("layout-error");
        $this->view->headScript()->appendFile('../../js/jquery/validation/registrationValidation.js');
        $login = new Application_Form_Login();
        $recoveryForm = new Application_Form_Recovery();

        $this->view->login = $login->getElements();
        $this->view->recovery = $recoveryForm->getElements();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getParams();
            if ($recoveryForm->isValid($formData)) {
                $model = new Application_Model_User();
                $email = $this->getRequest()->getParam('email');
                if ($email) {
                    $result = $model->passRecovery($email);
                    if ($result && $result != 2) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Your password was successfully changed. Please check your email to get new password'));
                        $this->_helper->redirector('recovery', 'user');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('failure' => 'There is no such e-mail in database'));
                        $this->_helper->redirector('recovery', 'user');
                    }
                } else {
                    $this->view->message = 'no e-mail';
                }
            } else {
                $this->view->message = 'form data is not valid';
            }
        }
    }

    public function loginAction()
    {

        $login = new Application_Form_Login();
        $this->view->form = $login;

        if ($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost();

            if ($login->isValid($data)) {
                $username = $this->getRequest()->getPost('username');
                $f = new Aimya_Filter_EmailToUsername();
                $username = $f->filter($username);
                $password = md5($this->getRequest()->getPost('password'));
                $userDb = new Application_Model_DbTable_Users();
                $user = $userDb->checkByUsername($username);
                if ($user['status'] == 1) {

                    Zend_Auth::getInstance()->getStorage()->read();

                    $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

                    $authAdapter->setTableName('user')
                        ->setIdentityColumn('username')
                        ->setCredentialColumn('password')
                        ->setIdentity($username)
                        ->setCredential($password);

                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($authAdapter);

                    if ($result->isValid()) {
                        $identity = $authAdapter->getResultRowObject();
                        $authStorage = $auth->getStorage();
                        $authStorage->write($identity);
                        $this->view->status = 1;


                        //$this->_helper->redirector('index', 'account');
                    } else {

                        $this->view->alertFlash = 'Authentication failed. Login or password are incorrect';
                    }
                }else if($user['status'] ==2){

                    $this->view->status = 2;

                }else{
                    $this->view->status = 0;
                }

            } else {
                $this->view->errors = $login->getErrors();
            }
        }
    }

    public function fbAction()
    {
        if ($this->getRequest()->isPost()) {

            $users = new Application_Model_DbTable_Users();
            $data = $this->getRequest()->getParam('data');


            $result = $users->checkByMail($data['email']);

            if($result == true){                        //LOGIN

                $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

                $authAdapter->setTableName('user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setIdentity($result['username'])
                    ->setCredential($result['password']);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $identity = $authAdapter->getResultRowObject();
                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);
                    $this->view->status = 1;


                    $this->view->success = 1;
                } else {

                    $this->view->alertFlash = 'Authentication failed. Login or password are incorrect';
                }
            }else{//registration\

                $profile = new Application_Model_DbTable_Profile();
                $onlineUser = new Application_Model_DbTable_OnlineUsers();
                $dbAvailability = new Application_Model_DbTable_Availability();
                $dbNotifications  = new Application_Model_DbTable_Notifications();
                $data['firstname'] = $data['first_name'];
                unset($data['first_name']);
                $data['lastname'] = $data['last_name'];
                unset($data['last_name']);
                unset($data['timezone']);

                $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
                $pass = array(); //remember to declare $pass as an array
                $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                for ($i = 0; $i < 8; $i++) {
                    $n = rand(0, $alphaLength);
                    $pass[] = $alphabet[$n];
                }
                $pass =  implode($pass); //turn the array into a string
                $data['password'] = $pass;
                $data['role'] = 2;
                $data['status'] = 1;
                $users->createUser($data);
                $lastId = $users->getAdapter()->lastInsertId();
                $profile->createProfile($lastId);

                $onlineUser->createLine($lastId);

                $dbAvailability->createAvailability($lastId);
                $dbNotifications->createNotifications($lastId);

                $folderModel = new Application_Model_Folder();
                $basePath = '/img/uploads/' . $lastId . '/avatar/base/';
                $mediumPath = '/img/uploads/' . $lastId . '/avatar/medium/';
                $thumbnailPath = '/img/uploads/' . $lastId . '/avatar/thumbnail/';
                $folderModel->createFolderChain($basePath, '/');
                $folderModel->createFolderChain($mediumPath, '/');
                $folderModel->createFolderChain($thumbnailPath, '/');



                $result = $users->checkByMail($data['email']);
                $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

                $authAdapter->setTableName('user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setIdentity($result['username'])
                    ->setCredential($result['password']);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $identity = $authAdapter->getResultRowObject();
                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);
                    $this->view->status = 1;


                   $this->view->success = 1;
                }else{
                    $this->view->fail = 1;
                }
            }

        }
    }
    public function linkedAction()
    {
        if ($this->getRequest()->isPost()) {

            $users = new Application_Model_DbTable_Users();
            $data = $this->getRequest()->getParam('data');

            $result = $users->checkByMail($data['emailAddress']);

            if($result == true){                        //LOGIN

                $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

                $authAdapter->setTableName('user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setIdentity($result['username'])
                    ->setCredential($result['password']);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $identity = $authAdapter->getResultRowObject();
                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);
                    $this->view->status = 1;


                    $this->view->success = 1;
                } else {

                    $this->view->alertFlash = 'Authentication failed. Login or password are incorrect';
                }
            }else{//registration\

                $profile = new Application_Model_DbTable_Profile();
                $onlineUser = new Application_Model_DbTable_OnlineUsers();
                $dbAvailability = new Application_Model_DbTable_Availability();
                $dbNotifications  = new Application_Model_DbTable_Notifications();
                $data['firstname'] = $data['firstName'];
                $data['email'] = $data['emailAddress'];
                $data['lastname'] = $data['lastName'];
                unset($data['firstName']);
                unset($data['lastName']);

                $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
                $pass = array(); //remember to declare $pass as an array
                $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                for ($i = 0; $i < 8; $i++) {
                    $n = rand(0, $alphaLength);
                    $pass[] = $alphabet[$n];
                }
                $pass =  implode($pass); //turn the array into a string
                $data['password'] = $pass;
                $data['username'] = $data['id'];
                $data['role'] = 2;
                $data['gender'] = 'male';
                $data['status'] = 1;
                $users->createUser($data);
                $lastId = $users->getAdapter()->lastInsertId();
                $profile->createProfile($lastId);

                $onlineUser->createLine($lastId);

                $dbAvailability->createAvailability($lastId);
                $dbNotifications->createNotifications($lastId);

                $folderModel = new Application_Model_Folder();
                $basePath = '/img/uploads/' . $lastId . '/avatar/base/';
                $mediumPath = '/img/uploads/' . $lastId . '/avatar/medium/';
                $thumbnailPath = '/img/uploads/' . $lastId . '/avatar/thumbnail/';
                $folderModel->createFolderChain($basePath, '/');
                $folderModel->createFolderChain($mediumPath, '/');
                $folderModel->createFolderChain($thumbnailPath, '/');



                $result = $users->checkByMail($data['emailAddress']);
                $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

                $authAdapter->setTableName('user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setIdentity($result['username'])
                    ->setCredential($result['password']);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $identity = $authAdapter->getResultRowObject();
                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);
                    $this->view->status = 1;


                    $this->view->success = 1;
                }else{
                    $this->view->fail = 1;
                }
            }

        }
    }
    public function registrationAction()
    {

        $reg = new Application_Form_Registration();
        if ($this->getRequest()->isPost()) {
            $modelUser = new Application_Model_User();

            $formData = $this->getRequest()->getPost();
            $this->view->data = $formData;
            if ($reg->isValid($formData)) {
                $user = new Application_Model_DbTable_Users();
                if ($user->checkByMail($formData['email'])) {
                    $this->view->alertFlash = 'This email already exist';
                } else if ($user->checkByUsername($formData['username'])) {
                    $this->view->alertFlash = 'This username already exist';

                } else {
                    $status = $modelUser->addNewUser($formData);

                    if ($status) {
                        $this->view->confirmFlash = 'Email Verification: An email has been sent to the email address you provided.  Please open that email and click on the provided link to activate your account.';
                    } else {
                        $this->view->alertFlash = 'Technical issues with email. Please try again later';
                    }
                }
            } else {

                $this->view->errors = $reg->getErrors();

            }
        }

    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index', 'index');
    }

    public function timezoneAction()
    {
        if ($this->getRequest()->getParam('timezone')) {
            $timezone = $this->getRequest()->getParam('timezone');

            $userTable = new Application_Model_DbTable_Users();

            $result = $userTable->getTimeZone();
            if ($result['timezone'] == '') {
                $status = $userTable->setDefaultTimezone($timezone);
                if ($status) {
                    $this->view->status = 'success';
                } else {
                    $this->view->status = 'error';
                }
            } else {
                $this->view->status = 'error';
            }
        }
        if ($this->getRequest()->getParam('time')) {
            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $userTable = new Application_Model_DbTable_Users();
            $currentTime = $userTable->getCurrentTime($userId);
            $this->view->time = $currentTime;

        }
    }

    public function resendAction()
    {
        if ($username = $this->getRequest()->getParam('username')) {
$config = array('auth' => 'login',
	         'port' => 465,
                'username' => 'no-reply@aimya.com',
                'password' => 'aimya');

            $transport = new Zend_Mail_Transport_Smtp('mail.aimya.com', $config);
            $f = new Aimya_Filter_EmailToUsername();
            $username = $f->filter($username);
            $userDb = new Application_Model_DbTable_Users();
            $user = $userDb->checkByUsername($username);
            $mail = new Aimya_Mail;
            $mail->setRecipient($user['email']);
            $mail->setTemplate(Aimya_Mail::SIGNUP_ACTIVATION);
            $mail->firstname = $user['firstname'];
            $mail->lastname = $user['lastname'];
            $mail->email = $user['email'];
            $mail->password = $this->getRequest()->getParam('password');
            $mail->token = $user['confirmation_token'];
            $mail->baseLink = "http://" . $_SERVER['HTTP_HOST'];
            if($mail->send()){
                $this->view->email = 1;
            }else{
                $this->view->emailError = 1;
            }

        }
    }
}