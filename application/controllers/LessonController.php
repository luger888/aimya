<?php

class LessonController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->AjaxContext()
            ->addActionContext('setup', 'json')
            ->addActionContext('index', 'json')
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
                //$helper = $this->view->getHelper('BaseLink');
                $broker = new Aimya_View_Helper_BaseLink();
                $baseLink = $broker->baseLink();

                $userModel = new Application_Model_DbTable_Users();
                $student = $userModel->getItem($studentId);
                $teacher = $userModel->getItem(Zend_Auth::getInstance()->getIdentity()->id);
                $lessonModel = new Application_Model_Lesson();
                $params = array(
                    'teacherStream'     => $teacher['username'],
                    'studentStream'     => $student['username'],
                    'presentationStream'=> $teacher['id'] . $student['id'],
                    'chatStream'        => $teacher['username'] . $student['username']
                );
                $resultParams = $lessonModel->createFlashParams($params);


                $flashObj = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash4/cabs/swflash.cab#version=4,0,0,0" id="name" width="400" height="400">
                             <param name="movie" value=' . $baseLink . '/flash/test4pix.swf">
                             <param value="' . $resultParams['teacherStream'] . '" name="teacherStream">
                             <param value="' . $resultParams['studentStream'] . '" name="studentStream">
                             <param value="' . $resultParams['presentationStream'] . '" name="presentationStream">
                             <param value="' . $resultParams['chatStream'] . '" name="chatStream">
                             <embed name="name" src="' . $baseLink . '/flash/test4pix.swf" quality="high" wmode="transparent" width="400" height="400" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed>
                             </object>';

                $this->view->flashObj = $flashObj;
            }
        }
    }

    public function detailsAction()
    {
        $this->_helper->layout()->disableLayout();
    }

}

