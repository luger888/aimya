<?php
class MessageController extends Zend_Controller_Action
{
    public function init()
    {
        $this   ->_helper->AjaxContext()
            ->initContext('json');
    }

    public function indexAction()
    {

    }

    public function inboxAction()
    {

    }

    public function sendAction()
    {
        $this->_helper->layout()->getView()->headTitle('New Message');

        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $form = new Application_Form_Message();
        $this->view->form = $form;
        $data = $this->getRequest()->isPost();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {

                $userTable = new Application_Model_DbTable_Users();
                $recipient = $userTable->checkByUsername($data['username']);

                if($recipient) {
                    $messageTable = new Application_Model_DbTable_Message();
                    $data['sender_id'] = $userId;
                    $data['recipient_id'] = $recipient['id'];
                    $sendStatus = $messageTable->sendMessage($data);
                    if($sendStatus){

                    }
                } else {
                    unset($data['username']);

                    $this->_helper->flashMessenger->addMessage(array('failure'=>'Problem with email'));
                    //$this->_helper->redirector('create', 'message');
                }

            }
        } else {


        }
        $form->populate($data);
        //$userData = $users->getUser($userId);
        //$form->populate($userData);
        //$this->view->data = $userData;
    }

    public function createAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function trashAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function archivedAction()
    {
        $this->_helper->layout()->disableLayout();
    }
}