<?php

class LessonController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->AjaxContext()
            ->addActionContext('setup', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('join', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {

        $this->_helper->layout()->getView()->headTitle('Friends');

        $userModel = new Application_Model_DbTable_Users();
        $userList = $userModel->getItemsList();

        $this->view->userList = $userList;

    }

    public function setupAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isXmlHttpRequest()){
            $studentId = $this->getRequest()->getParam('student_id');
            if(isset($studentId)) {
                $broker = new Aimya_View_Helper_BaseLink();
                $baseLink = $broker->baseLink();

                $userModel = new Application_Model_DbTable_Users();
                $student = $userModel->getItem($studentId);
                $teacher = $userModel->getItem(Zend_Auth::getInstance()->getIdentity()->id);
                $lessonModel = new Application_Model_Lesson();
                $params = array(
                    'teacherStream'     => $teacher['username'],
                    'studentStream'     => $student['username'],
                    'soID'   => $teacher['id'] . $student['id'],
                );

                $resultParams = $lessonModel->createFlashParams($params);

                $dataToInsert = array(
                    'creator_id' => $teacher['id'],
                    'partner_id' => $student['id'],
                    'creator_stream_name' => $resultParams['teacherStream'],
                    'partner_stream_name' => $resultParams['studentStream'],
                    'so_id' => $resultParams['soID'],
                    'status' => 1,
                );

                $lessonModel = new Application_Model_DbTable_Lesson();
                $lessonModel->startLesson($dataToInsert);


                $flashObj = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash4/cabs/swflash.cab#version=4,0,0,0" id="name" width="800" height="800">
                             <param name="movie" value=' . $baseLink . '/flash/aimia_lesson.swf">
                             <param value="' . $resultParams['teacherStream'] . '" name="myStreamName">
                             <param value="' . $resultParams['studentStream'] . '" name="partnerStreamName">
                             <param value="' . $resultParams['soID'] . '" name="soID">
                             <param value="' . Zend_Auth::getInstance()->getIdentity()->username . '" name="userName">
                             <embed name="name" src="' . $baseLink . '/flash/aimia_lesson.swf" quality="high" wmode="transparent" width="800" height="800" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed>
                             </object>';

                $this->view->flashObj = $flashObj;
            }
        }
    }

    public function detailsAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function joinAction()
    {
        $this->_helper->layout()->disableLayout();

        $studentId = Zend_Auth::getInstance()->getIdentity()->id;
        $lessonTable = new Application_Model_DbTable_Lesson();

        $result = $lessonTable->checkAvailableLesson($studentId);

        if($result) {
            $broker = new Aimya_View_Helper_BaseLink();
            $baseLink = $broker->baseLink();

            $userModel = new Application_Model_DbTable_Users();
            //$student = $userModel->getItem(Zend_Auth::getInstance()->getIdentity()->id);
            //$teacher = $userModel->getItem($result['creator_id']);

            $lessonModel = new Application_Model_DbTable_Lesson();
            $lessonModel->changeStatus($result['id']);


            $flashObj = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash4/cabs/swflash.cab#version=4,0,0,0" id="name" width="800" height="800">
                             <param name="movie" value=' . $baseLink . '/flash/aimia_lesson.swf">
                             <param value="' . $result['creator_stream_name'] . '" name="partnerStreamName">
                             <param value="' . $result['partner_stream_name'] . '" name="myStreamName">
                             <param value="' . $result['so_id'] . '" name="soID">
                             <param value="' . Zend_Auth::getInstance()->getIdentity()->username . '" name="userName">
                             <embed name="name" src="' . $baseLink . '/flash/aimia_lesson.swf" quality="high" wmode="transparent" width="800" height="800" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed>
                             </object>';

            $this->view->flashObj = $flashObj;
            $this->view->responce = $result;
            $this->view->result = true;
        } else {
            $this->view->result = false;
        }

    }

}

