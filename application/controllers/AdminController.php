<?php
class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->view->headScript()->appendFile('../../../js/jquery/admin/admin.js');
        $this->_helper->AjaxContext()
            ->addActionContext('index', 'json')
            ->initContext('json');


    }

    public function indexAction()
    {
        $lessonCatDb = new Application_Model_DbTable_LessonCategory();
        $categories = $lessonCatDb->getLessonCategories();
        $this->view->categories = $categories;

        $lessonDurDb = new Application_Model_DbTable_LessonDuration();
        $durations = $lessonDurDb->getLessonDurations();
        $this->view->durations = $durations;
        if ($this->getRequest()->isPost()) {

            if ($this->getRequest()->getParam('deleteCat')) {
                $result = $lessonCatDb->removeCat($this->getRequest()->getParam('deleteCat'));
                if ($result) {
                    $this->view->result = 1;
                } else {
                    $this->view->fail = 1;
                }
            }

            if ($this->getRequest()->getParam('deleteDur')) {
                $result = $lessonDurDb->removeDuration($this->getRequest()->getParam('deleteDur'));
                if ($result) {
                    $this->view->result = 1;
                } else {
                    $this->view->fail = 1;
                }
            }

            if ($this->getRequest()->getParam('categories')) {
                 $lessonCatDb->addCats($this->getRequest()->getParams());
            }
            if ($this->getRequest()->getParam('durations')) {
                $lessonDurDb->addDurations($this->getRequest()->getParams());
            }
        }
    }

    public function usersAction()
    {

    }

    public function paymentsAction()
    {

    }

    public function staticAction()
    {

    }

    public function metricsAction()
    {

    }
}