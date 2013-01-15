<?php

class ReviewController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
    }

    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $reviewDb = new Application_Model_DbTable_Review();
        $reviewModel = new Application_Model_Review();
        $filterForm = new Application_Form_LessonFilter();
        $this->view->filterForm = $filterForm;
        $this->view->stars = $reviewModel->getTotalReviews($identity->id);

        if ($this->getRequest()->isPost()) {
            $this->view->filterInfo = '<div class="floatRight">From: ' .$this->getRequest()->getParam('fromPeriod') .' To: ' .$this->getRequest()->getParam('toPeriod') .'</div>';
            $this->view->reviews = $reviewDb->getFullReviews($identity->id, $this->getRequest()->getParam('fromPeriod'), $this->getRequest()->getParam('toPeriod'));
        }else{
            $this->view->reviews = $reviewDb->getFullReviews($identity->id);

        }

    }



}

