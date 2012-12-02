<?php

class SearchController extends Aimya_Controller_BaseController
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
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
        $this->view->hits=$paginator;
    }

    public function reindexAction()
    {

        $serviceTable = new Application_Model_DbTable_ServiceDetail();
        $index = Zend_Search_Lucene::create(realpath(APPLICATION_PATH . '/data/search_indexes'));
        $services = $serviceTable->getServices();
        foreach($services as $service)
        {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Text('lesson_category', $service['lesson_category'])); //here field = ur database column
            $doc->addField(Zend_Search_Lucene_Field::Text('subcategory',$service['subcategory']));
            $doc->addField(Zend_Search_Lucene_Field::Text('user_id',$service['user_id']));
            $index->addDocument($doc);
        }
        $this->view->servicesCount = count($services);
    }


}









