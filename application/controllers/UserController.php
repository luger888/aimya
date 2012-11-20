<?php
class UserController extends Zend_Controller_Action
{
    public function init()
    {
        $this   ->_helper->AjaxContext()
            ->addActionContext('registration','json')
            ->addActionContext('login','json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $userId = $this->getRequest()->getParam('id');

        $userModel = new Application_Model_DbTable_Users();

        $user = $userModel->getFullData($userId);
        if($user) {
            $userData = $user;
        } else {
            $this->_helper->redirector('page404','error');
        }
        $this->view->userData = $userData;
    }

    public function confirmationAction() {

        $data = $this->getRequest()->getParams();

        if(isset($data)){
            if (!$data['query_id']) {
                $this->view->message = 'no email';
            } else if (!$data['query_key']) {
                $this->view->message = 'no token';
            } else if (!$data['query_key'] && !$data['query_id']) {

                $this->view->message = 'no data';

            } else {

                $model = new Application_Model_User();
                $this->view->message = $model->approveUser($data);

                $this->_helper->flashMessenger->addMessage(array('success'=>'Account confirmed. Please login to your account'));
                $this->_helper->redirector('index', 'index');

            }
        } else {
            $this->view->message = 'request data is incorrect';
        }

    }

    public function recoveryAction()
    {
        if($this->getRequest()->isPost()){

            $model = new Application_Model_User();
            $email = $this->getRequest()->getPost('email');
            if ($email) {
                $this->view->message = $model->passRecovery($email);
            } else {
                $this->view->message = 'no e-mail';
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
                    if ($identity->status == '0') {
                        $this->_helper->flashMessenger->addMessage(array('failure'=>'Account is not confirmed. Please check you email and confirm registration'));
                        $this->_helper->redirector('index', 'index');
                    }else{
                        $this->view->status = '1';

                    }
                }else{
                    $this->view->error = 'Authentication failed.';
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
                if ($user->checkByMail($formData['email']) || $user->checkByUsername($formData['username'])) {
                    $this->view->confirmFlash = 'This email or username already exist';

                } else {
                    $status = $modelUser->addNewUser($formData);

                    if($status) {
                        $this->view->confirmFlash = 'Please confirm your email';
                    } else {
                        $this->view->confirmFlash = 'Some tricky error';
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
}