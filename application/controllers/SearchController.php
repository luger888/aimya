<?php

class SearchController extends Aimya_Controller_BaseController
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->initContext('json');
    }

    public function searchAction()
    {
        $indexPath = realpath(APPLICATION_PATH . '/data/search_indexes');
        $index = Zend_Search_Lucene::open($indexPath);
        $searchString = $this->getRequest()->getParam('search_string');
        $hits = $index->find($searchString);
        $paginator = Zend_Paginator::factory($hits);
        //$paginator->setCurrentPageNumber($this->_getParam('page'));
        //$paginator->setItemCountPerPage(10);

        $userTable = new Application_Model_DbTable_Users();
        $usersArray = array();
        foreach($paginator as $item){
            $usersArray[]= $userTable->getFullData($item->user_id);
        }
        $this->view->hits=$paginator;
        $this->view->users=$usersArray;
    }

    public function reindexAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        //$folderModel = new Application_Model_DbTable_Folder();
        //$folderModel->createFolderChain();

        $serviceTable = new Application_Model_DbTable_ServiceDetail();
        @mkdir(realpath(APPLICATION_PATH) . DIRECTORY_SEPARATOR . 'data');
        @mkdir(realpath(APPLICATION_PATH) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'search_indexes');

        $index = Zend_Search_Lucene::create(realpath(APPLICATION_PATH . '/data/search_indexes'));
        $dbUserModel = new Application_Model_DbTable_Users();

        $services = $serviceTable->getServices();
        $users = $dbUserModel->getUsers();
        foreach($services as $service)
        {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Text('lesson_category', $service['lesson_category'])); //here field = ur database column
            $doc->addField(Zend_Search_Lucene_Field::Text('subcategory',$service['subcategory']));
            $doc->addField(Zend_Search_Lucene_Field::Text('user_id',$service['user_id']));
            $index->addDocument($doc);
        }
        foreach($users as $user)
        {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Text('username', $user['username'])); //here field = ur database column
            $doc->addField(Zend_Search_Lucene_Field::Text('user_id',$user['id']));
            $index->addDocument($doc);
        }
        $this->view->servicesCount = count($services);
    }


}









