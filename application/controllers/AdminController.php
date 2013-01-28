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
        $usersDb = new Application_Model_DbTable_Users();
        $this->view->userList = $usersDb->getUsers();

        if ($this->getRequest()->isPost()) {

            if ($this->getRequest()->getParam('suspendId')) {
               $usersDb->changeUserStatus($this->getRequest()->getParam('suspendId'), $this->getRequest()->getParam('status'));
            }
        }

    }

    public function paymentsAction()
    {
        $usersDb = new Application_Model_DbTable_Users();
        $subscrDb = new Application_Model_DbTable_Subscriptions();
        $this->view->userList = $usersDb->getUsersRefunds();
       // $subscrDb->getLatestSubscription();
    }

    public function staticAction()
    {

    }

    public function metricsAction()
    {
        $userDb = new Application_Model_DbTable_Users();
        $subscrDb = new Application_Model_DbTable_Subscriptions();
        $lessonDb = new Application_Model_DbTable_Lesson();
        $ordersDb = new Application_Model_DbTable_Orders();
        $this->view->usersCount = $userDb->getUsersCount();
        $this->view->lessonsCount = $lessonDb->getLessons();
        $incomeLessons = $ordersDb->getLessonIncome();
        $incomeSubscr = $subscrDb->getSubscriptionsIncome();
        $income = array();
        $income['totalIncome'] = $incomeLessons['allTime']['lessonIncome']['sum'] + $incomeSubscr['allTime']['subscrIncome']['sum'];
        $income['lastMonthIncome'] = $incomeLessons['lastMonth']['lessonIncome']['sum'] + $incomeSubscr['lastMonth']['subscrIncome']['sum'];
        $this->view->income = $income;
    }
}