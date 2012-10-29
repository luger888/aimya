<?php
class UserController extends Zend_Controller_Action
{
    private $aclUndefined = 0;
    private $aclPending = 1;
    private $aclUser = 2;
    private $aclAdmin = 3;

    public function init()
    {


    }

    public function indexAction()
    {

    }

    public function loginAction()
    {

        $login = new Application_Form_Login();
        $this->view->form = $login;

        if ($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost();

            if($login->isValid($data)){

                $username = $this->getRequest()->getPost('username');
                $password = md5($this->getRequest()->getPost('password'));
                $identity = Zend_Auth::getInstance()->getStorage()->read();

                $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

                $authAdapter    ->setTableName('user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setIdentity($username)
                    ->setCredential($password);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if($result->isValid()){

                    $identity = $authAdapter->getResultRowObject();
                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);

                    /* if($identity->role == $this->aclPending){

                        $this->_helper->redirector('index', 'index'); //////////////another redirect

                    }
                    else{


                      if($this->getRequest()->getPost('remember')){

                            setcookie("email", $this->getRequest()->getPost('email') ,time() + 3600 * 24 * 365 , '/');

                        }
                        else{

                            setcookie("email", '' ,time() + 3600 * 24 * 365 , '/');

                        }

                        $this->_helper->redirector('index', 'account');
                    }*/

                }
                else{

                    $this->view->warning = 1;

                }
            }
        }
    }

    public function registrationAction()
    {

        $reg = new Application_Form_Registration();
        if($this->getRequest()->isPost()){
            $model = new Application_Model_User();

            $formData = $this->getRequest()->getPost();
            $this->view->data = $formData;
            if ($reg->isValid($formData)) {

                $status = $model->addNewUser($formData);
                $this->_helper->redirector('index', 'index'); ///////account redirect!

            } else {

                $this->view->errors = $reg->getErrors();

            }
        }

    }
}