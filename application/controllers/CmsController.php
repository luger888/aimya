<?php

class CmsController extends Aimya_Controller_BaseController
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('index', 'json')
            ->addActionContext('new', 'json')
            ->addActionContext('edit', 'json')
            ->addActionContext('delete', 'json')
            ->addActionContext('view', 'json')
            ->initContext('json');
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
                $content = $this->getRequest()->getParam('content');

                $cms = new Application_Model_DbTable_Cms();
                $check = $cms->getPageByUri($uri);
                if($check['id']){

                    $this->view->message = 'this uri already exists';

                }else{

                    $cms->createStatisPage($name, $uri, $content);
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

        $cms = new Application_Model_DbTable_Cms();
        $cms->deleteItem($this->getRequest()->getParam('id'));
        $this->_helper->redirector('index', 'cms');

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
                $content = $this->getRequest()->getParam('content');

                $cms->updateStaticPage($id, $name, $uri, $content);

            } else {
                $this->view->errors = $form->getErrors();
            }

        }
        $c = $cms->getItem($id);
        if(!$c) { $this->_helper->redirector('page404', 'error'); }

        $this->view->form = $form->populate($c);
    }

    public function viewAction()
    {

        $uri = $this->getRequest()->getParam('uri');
        $cms = new Application_Model_DbTable_Cms();
        $data = $cms->getPageByUri($uri);

        if(!$data)$this->_helper->redirector('page404', 'error');

        $this->view->content = $data;

    }


}









