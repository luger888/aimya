<?php
class FriendsController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this   ->_helper->AjaxContext()
            ->addActionContext('send', 'json')
            ->addActionContext('autocomplete', 'json')
            ->initContext('json');
    }

    public function sendAction()
    {

        $form = new Application_Form_Friend();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {

                $friendId = $this->getRequest()->getParam('friend_id');
                $friendTable = new Application_Model_DbTable_Friends();
                $url = $this->getRequest()->getParam('url');
                $url = substr($url, 3);
                $message = $this->getRequest()->getParam('request_comment');
                $result = $friendTable->addFriend($friendId, $message);

                if($result) {
                    $notesDb = new Application_Model_Notifications();
                    $notesDb->sendAlerts($friendId, 'friend');//send email if needed
                    $this->_helper->flashMessenger->addMessage(array('success'=>'Request successfully sent'));
                    $this->redirect($url);
                } else {
                    $this->_helper->flashMessenger->addMessage(array('failure'=>'Problem with sending request, please try again later'));
                    $this->redirect($url);
                }
            }
        } elseif($this->getRequest()->isGet()) {
            if($this->getRequest()->getParam('friend_id')){
                $friendId = $this->getRequest()->getParam('friend_id');
                $friendTable = new Application_Model_DbTable_Friends();
                $url = $this->getRequest()->getParam('url');
                $url = substr($url, 3);
                $message = '';
                $result = $friendTable->addFriend($friendId, $message);

                if($result) {
                    $this->_helper->flashMessenger->addMessage(array('success'=>'You successfully added this user to your account'));
                    $this->redirect($url);
                } else {
                    $this->_helper->flashMessenger->addMessage(array('failure'=>'Problem with sending request, please try again later'));
                    $this->redirect($url);
                }
            }
        } else {
            if($this->getRequest()->getParam('friend_id')){
                $friendId = $this->getRequest()->getParam('friend_id');
                $friendTable = new Application_Model_DbTable_Friends();

                $message = '';
                $result = $friendTable->addFriend($friendId, $message);

                if($result) {
                    $this->view->result = $result;
                    $this->view->successFlash = "Request successfully sent";
                } else {
                    $this->view->alertFlash = 'Problem with sending request, please try again later';
                }
            }
        }

    }

    public function listAction()
    {

    }
}