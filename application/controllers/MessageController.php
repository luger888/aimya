<?php
class MessageController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this   ->_helper->AjaxContext()
            ->addActionContext('count', 'json')
            ->addActionContext('massdelete', 'json')
            ->addActionContext('massarchive', 'json')
            ->addActionContext('masstrash', 'json')
            ->addActionContext('massrestore', 'json')
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
        $activity = new Application_Model_DbTable_OnlineUsers();
        $user = new Application_Model_DbTable_Users();

        $messages = $messageTable->getInbox($userId);
        foreach ($messages as $index => $value){
            $username = $user->getUser($value['sender_id']);
            $messages[$index]['isActive'] = $activity->isOnline($value['sender_id']);//check if user online
            $messages[$index]['username'] = $username['username'];//check if user online
        }
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
                    $friendsDb= new Application_Model_DbTable_Friends();
                    $isBlocked = $friendsDb->isBlocked($recipient['id']);
                    if(!$isBlocked){
                        $sendStatus = $messageTable->sendMessage($data);
                    }else{
                        $sendStatus = $messageTable->sendMessage($data, 4);
                    }

                    if($sendStatus){
                        $notesDb = new Application_Model_Notifications();
                        $notesDb->sendAlerts($recipient['id'], 'message');//sending email if needed
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
        $friendTable = new Application_Model_DbTable_Friends();
        $friends = $friendTable->getActiveFriends();

        $this->view->friends = $friends;
    }

    public function sentAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_helper->layout()->getView()->headTitle('Sent Messages');
        //$this->_helper->layout()->disableLayout();
        $messageTable = new Application_Model_DbTable_Message();
        $messageActionsForm = new Application_Form_MessageActions();

        $messages = $messageTable->getSent($userId);
        $activity = new Application_Model_DbTable_OnlineUsers();
        $user = new Application_Model_DbTable_Users();
        foreach ($messages as $index => $value){
            $username = $user->getUser($value['sender_id']);
            $messages[$index]['isActive'] = $activity->isOnline($value['sender_id']);//check if user online
            $messages[$index]['username'] = $username['username'];//check if user online
        }
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

        $activity = new Application_Model_DbTable_OnlineUsers();
        $user = new Application_Model_DbTable_Users();

        foreach ($messages as $index => $value){
            $username = $user->getUser($value['sender_id']);
            $messages[$index]['isActive'] = $activity->isOnline($value['sender_id']);//check if user online
            $messages[$index]['username'] = $username['username'];//check if user online
        }
        $this->view->messageActions = $messageActionsForm;
        $this->view->messages = $messages;
    }

    public function archivedAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_helper->layout()->getView()->headTitle('Archived Messages');
        //$this->_helper->layout()->disableLayout();
        $messageTable = new Application_Model_DbTable_Message();
        $messageActionsForm = new Application_Form_MessageActions();

        $messages = $messageTable->getArchived($userId);

        $activity = new Application_Model_DbTable_OnlineUsers();
        $user = new Application_Model_DbTable_Users();

        foreach ($messages as $index => $value){
            $username = $user->getUser($value['sender_id']);
            $messages[$index]['isActive'] = $activity->isOnline($value['sender_id']);//check if user online
            $messages[$index]['username'] = $username['username'];//check if user online
        }
        $this->view->messageActions = $messageActionsForm;
        $this->view->messages = $messages;
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
        $this->_helper->layout()->getView()->headTitle('Forward Message');
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
                    $data = $this->getRequest()->getParams();
                    $userTable = new Application_Model_DbTable_Users();

                    $recipient = $userTable->checkByUsername($data['username']);

                    if($recipient) {
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
                    }
                }
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('failure'=>'Some problem, try again later'));
            $this->_helper->redirector($this->getRequest()->getParam('current_action'), 'message');
        }
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

    public function viewAction()
    {
        $this->_helper->layout()->getView()->headTitle('View Message');
        if($this->getRequest()->getParam('message_id')) {
            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $messageTable = new Application_Model_DbTable_Message();
            if($this->getRequest()->getParam('status')) {
                $messageTable->readMessage($this->getRequest()->getParam('message_id'), $userId);
            }
            $message = $messageTable->getMessage($this->getRequest()->getParam('message_id'), $userId);
            $userTable = new Application_Model_DbTable_Users();
            $sender = $userTable->getItem($message['sender_id']);

            $this->view->user = $sender;
            $this->view->message = $message;

        } else {
            //$this->_helper->flashMessenger->addMessage(array('failure'=>'Please try again later'));
        }
    }

    public function countAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $messageTable = new Application_Model_DbTable_Message();
        $messageCount = $messageTable->getNewMessagesCount($userId);

        $this->view->messageCount = $messageCount;

    }

    public function massdeleteAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->getParam('message_ids') && $this->getRequest()->getParam('action')) {

            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $messageTable = new Application_Model_DbTable_Message();
            $messageCount = $messageTable->massDelete($this->getRequest()->getParam('message_ids'), $userId, $this->getRequest()->getParam('current_action'));

            $this->view->messageCount = $messageCount;
        } else {
            $this->view->messageCount = "Bad parameters";
        }

    }

    public function masstrashAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->getParam('message_ids') && $this->getRequest()->getParam('action')) {

            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $messageTable = new Application_Model_DbTable_Message();
            $messageCount = $messageTable->massTrash($this->getRequest()->getParam('message_ids'), $userId);

            $this->view->messageCount = $messageCount;
        } else {
            $this->view->messageCount = "Bad parameters";
        }

    }

    public function massarchiveAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->getParam('message_ids') && $this->getRequest()->getParam('action')) {

            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $messageTable = new Application_Model_DbTable_Message();
            $messageCount = $messageTable->massArchive($this->getRequest()->getParam('message_ids'), $userId, $this->getRequest()->getParam('current_action'));

            $this->view->messageCount = $messageCount;
        } else {
            $this->view->messageCount = "Bad parameters";
        }

    }

    public function massrestoreAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->getParam('message_ids') && $this->getRequest()->getParam('action')) {

            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $messageTable = new Application_Model_DbTable_Message();
            $messageCount = $messageTable->massRestore($this->getRequest()->getParam('message_ids'), $userId, $this->getRequest()->getParam('current_action'));

            $this->view->messageCount = $messageCount;
        } else {
            $this->view->messageCount = "Bad parameters";
        }

    }
}