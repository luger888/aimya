<?php
class MessageController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this   ->_helper->AjaxContext()
            ->initContext('json');
    }

    public function indexAction()
    {

    }

    public function inboxAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_helper->layout()->getView()->headTitle('Inbox Messages');
        //$this->_helper->layout()->disableLayout();
        $messageTable = new Application_Model_DbTable_Message();
        $messageActionsForm = new Application_Form_MessageActions();

        $messages = $messageTable->getInbox($userId);

        $this->view->messageActions = $messageActionsForm;
        $this->view->messages = $messages;
    }

    public function sendAction()
    {
        //$this->_helper->layout()->disableLayout();
        $this->_helper->layout()->getView()->headTitle('New Message');

        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $form = new Application_Form_Message();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                //$form->populate($data);
                $data = $this->getRequest()->getParams();
                $userTable = new Application_Model_DbTable_Users();
                //echo $data['username'];

                $recipient = $userTable->checkByUsername($data['username']);

                if($recipient) {
                    //die('1');
                    $messageTable = new Application_Model_DbTable_Message();
                    $data['sender_id'] = $userId;
                    $data['recipient_id'] = $recipient['id'];
                    $sendStatus = $messageTable->sendMessage($data);
                    if($sendStatus){
                        $this->_helper->flashMessenger->addMessage(array('success'=>'Message sent'));
                        $this->_helper->redirector('inbox', 'message');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('failure'=>'Error with sending, please try again later'));
                    }
                } else {
                    //die('2');
                    unset($data['username']);

                    $this->_helper->flashMessenger->addMessage(array('failure'=>'Unknown recipient'));
                    //$this->_helper->redirector('create', 'message');
                }

            }
        }
        //$userData = $users->getUser($userId);
        //$form->populate($userData);
        //$this->view->data = $userData;
    }

    public function sentAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_helper->layout()->getView()->headTitle('Inbox Messages');
        //$this->_helper->layout()->disableLayout();
        $messageTable = new Application_Model_DbTable_Message();
        $messageActionsForm = new Application_Form_MessageActions();

        $messages = $messageTable->getSent($userId);

        $this->view->messageActions = $messageActionsForm;
        $this->view->messages = $messages;
    }

    public function trashAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_helper->layout()->getView()->headTitle('Inbox Messages');
        //$this->_helper->layout()->disableLayout();
        $messageTable = new Application_Model_DbTable_Message();
        $messageActionsForm = new Application_Form_MessageActions();

        $messages = $messageTable->getTrash($userId);

        $this->view->messageActions = $messageActionsForm;
        $this->view->messages = $messages;
    }

    public function archiveAction()
    {
        //$this->_helper->layout()->disableLayout();
    }
    public function replyAction()
    {
        $this->_helper->layout()->getView()->headTitle('Reply Message');
        if($this->getRequest()->getParam('message_id')) {
            $userId = Zend_Auth::getInstance()->getIdentity()->id;

            $form = new Application_Form_Message();
            $this->view->form = $form;

            $messageTable = new Application_Model_DbTable_Message();
            $message = $messageTable->getReplyMessage($this->getRequest()->getParam('message_id'), $userId);
            $userTable = new Application_Model_DbTable_Users();
            $sender = $userTable->getItem($message['sender_id']);

            $this->view->sender = $sender;
            $this->view->message = $message;

            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getParams())) {
                    //$form->populate($data);
                    $data = $this->getRequest()->getParams();
                    $userTable = new Application_Model_DbTable_Users();

                    $recipient = $userTable->checkByUsername($data['username']);

                    if($recipient) {
                        //die('1');
                        $messageTable = new Application_Model_DbTable_Message();
                        $data['sender_id'] = $userId;
                        $data['recipient_id'] = $recipient['id'];
                        $sendStatus = $messageTable->sendMessage($data);

                        if($sendStatus){
                            $this->_helper->flashMessenger->addMessage(array('success'=>'Message sent'));
                            $this->_helper->redirector('inbox', 'message');
                        } else {
                            $this->_helper->flashMessenger->addMessage(array('failure'=>'Error with sending, please try again later'));
                        }
                    } else {
                        unset($data['username']);

                        $this->_helper->flashMessenger->addMessage(array('failure'=>'Unknown recipient'));
                        //$this->_helper->redirector('create', 'message');
                    }

                }
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('failure'=>'Some problem, try again later'));
            $this->_helper->redirector($this->getRequest()->getParam('current_action'), 'message');
        }
    }

    public function forwardAction()
    {
        //$this->_helper->layout()->disableLayout();
    }

    public function deleteAction()
    {
        if($this->getRequest()->getParam('message_id'))
        {
            if($this->getRequest()->getParam('current_action')) {
                $messageTable = new Application_Model_DbTable_Message();
                $deleteMessage = $messageTable->deleteMessage($this->getRequest()->getParam('message_id'), $this->getRequest()->getParam('current_action'));

                if($deleteMessage) {
                    $this->_helper->flashMessenger->addMessage(array('success'=>'Message successfully removed'));
                    $this->_helper->redirector($this->getRequest()->getParam('current_action'), 'message');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('failure'=>'Some problem, try again later'));
                    $this->_helper->redirector($this->getRequest()->getParam('current_action'), 'message');
                }
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('failure'=>'Please try again later'));
        }
        //$this->_redirect($url, array('prependBase' => false));
    }
}