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
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $form = new Application_Form_Friend();
        $this->view->form = $form;

        /*if ($this->getRequest()->isPost()) {
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
        }*/
    }

    public function listAction()
    {

    }
}