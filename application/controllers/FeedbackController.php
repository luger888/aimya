<?php

class FeedbackController extends Aimya_Controller_BaseController
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('form', 'json')
            ->addActionContext('view', 'json')
            ->addActionContext('create', 'json')
            ->initContext('json');
    }

    public function createAction()
    {
        $feedbackContent = $this->getRequest()->getParam('feedbackContent');
        $teacherId = Zend_Auth::getInstance()->getIdentity()->id;
        $lessonId = $this->getRequest()->getParam('lessonId');

        if($feedbackContent && $teacherId &&$lessonId) {
            $feedbackTable = new Application_Model_DbTable_LessonFeedback();
            $res = $feedbackTable->createFeedback($feedbackContent, $teacherId, $lessonId);
            if($res) {
                $this->_helper->redirector('details', 'lesson');
            } else {
                $this->_helper->redirector('details', 'lesson');
            }
        }

    }

    public function viewAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            if($this->getRequest()->getParam('lessonId')) {
                $this->_helper->layout()->disableLayout();
                $feedbackTable = new Application_Model_DbTable_LessonFeedback();
                $feedback = $feedbackTable->getFeedbackByLesson($this->getRequest()->getParam('lessonId'));
                $lessonTable = new Application_Model_DbTable_Lesson();
                $reviewTable = new Application_Model_DbTable_Review();
                $lesson = $lessonTable->getLessonByUser($this->getRequest()->getParam('lessonId'));
                $review = $reviewTable->getReviews($this->getRequest()->getParam('lessonId'));

                $now = time(); // or your date as well
                $your_date = strtotime($lesson['created_at']);
                $datediff = $your_date - $now;
                $reviewDate = floor($datediff / (60 * 60 * 24) + 10);
                $this->view->review = $review['review'];
                $this->view->rate = $review['rating'];
                $this->view->date = $reviewDate;
                $this->view->feedback = $feedback;
                $html = $this->view->render('feedback/view.phtml');
                $this->view->html = $html;

            }
        }
    }

    public function formAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            if($this->getRequest()->getParam('lessonId')) {
                $this->_helper->layout()->disableLayout();
                $feedbackForm = new Application_Form_Feedback();
                $feedbackForm->lessonId->setValue($this->getRequest()->getParam('lessonId'));
                $this->view->feedbackForm = $feedbackForm;
                $htmlString = $this->view->render('feedback/form.phtml');
                $this->view->html = $htmlString;
            }

        }
    }
}









