<?php
class FriendsController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this   ->_helper->AjaxContext()
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

                $result = $friendTable->addFriend($friendId);

                if($result) {
                    $this->_helper->flashMessenger->addMessage(array('success'=>'Request successfully sent'));
                    $this->_redirect($url);
                } else {
                    $this->_helper->flashMessenger->addMessage(array('failure'=>'Problem with sending request, please try again later'));
                    $this->_redirect($url);
                }
            }
        }
    }

    public function listAction()
    {

    }
}