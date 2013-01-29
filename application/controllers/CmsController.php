<?php

class CmsController extends Aimya_Controller_BaseController
{

    public function init()
    {
        $this->_helper->layout->setLayout("layout");
        $this->_helper->AjaxContext()
            ->addActionContext('index', 'json')
            ->addActionContext('new', 'json')
            ->addActionContext('edit', 'json')
            ->addActionContext('delete', 'json')
            ->addActionContext('view', 'json')
            ->initContext('json');
        $login = new Application_Form_Login();

        $this->view->login = $login->getElements();
    }

    public function indexAction()
    {
        $cms = new Application_Model_DbTable_Cms();
        $this->view->list = $cms->getItemsList();
    }

    public function newAction()
    {

        $form = new Application_Form_Cms();

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();


            if ($form->isValid($formData)) {

                $name = $this->getRequest()->getParam('name');
                $uri = $this->getRequest()->getParam('uri');
                $language = $this->getRequest()->getParam('language');
                $content = $this->getRequest()->getParam('contentCKE');

                $cms = new Application_Model_DbTable_Cms();
                $check = $cms->getPageByUri($uri, $language);
                if ($check['id']) {
                    $this->_helper->flashMessenger->addMessage(array('fail' => 'this uri already exists'));
                    $this->_helper->redirector('new', 'cms');


                } else {

                    $cms->createStaticPage($name, $uri, $language, $content);
                    $this->_helper->redirector('index', 'cms');

                }

            } else {
                $this->view->errors = $form->getErrors();
            }
        }

        $this->view->form = $form;

    }

    public function deleteAction()
    {
        if ($this->getRequest()->isGet()) {
            $cms = new Application_Model_DbTable_Cms();
            $cms->deletePage($this->getRequest()->getParam('id'));
            $this->_helper->redirector('index', 'cms');
        }

    }

    public function editAction()
    {

        $cms = new Application_Model_DbTable_Cms();
        $form = new Application_Form_Cms();
        $id = $this->getRequest()->getParam('id');

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {

                $id = $this->getRequest()->getParam('id');
                $name = $this->getRequest()->getParam('name');
                $uri = $this->getRequest()->getParam('uri');
                $language = $this->getRequest()->getParam('language');
                $content = $this->getRequest()->getParam('contentCKE');
                $cms = new Application_Model_DbTable_Cms();
                $check = $cms->ifAlreadyExist($id, $uri, $language);
                if (!$check['id']) {
                    $cms->updateStaticPage($id, $name, $uri, $language, $content);
                    $this->_helper->redirector('index', 'cms');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('fail' => 'this uri already exists'));
                    //$this->_helper->redirector('new', 'cms');

                }
            } else {
                $this->view->errors = $form->getErrors();
            }

        }
        $c = $cms->getItem($id);
        if (!$c) {
            $this->_helper->redirector('page404', 'error');
        }

        $this->view->form = $form->populate($c);
    }

    public function viewAction()
    {

        $language = substr(Zend_Controller_Front::getInstance()->getBaseUrl(), '1');

        $uri = $this->getRequest()->getParam('uri');
        $cms = new Application_Model_DbTable_Cms();
        $data = $cms->getPageByUri($uri, $language);

        if (!$data) $this->_helper->redirector('page404', 'error');

        $this->view->content = $data;

    }


}









