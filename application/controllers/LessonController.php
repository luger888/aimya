<?php

class LessonController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this->_helper->AjaxContext()
            ->addActionContext('setup', 'json')
            ->addActionContext('index', 'json')
            //->addActionContext('join', 'json')
            ->addActionContext('upload', 'json')
            ->addActionContext('files', 'json')
            ->addActionContext('end', 'json')
            ->addActionContext('updatesize', 'json')
            ->addActionContext('getsize', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {

        $this->_helper->layout()->getView()->headTitle('Friends');

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $lessonTable = new Application_Model_DbTable_Lesson();

        $lesson = $lessonTable->checkAvailableLesson($userId);

        $userModel = new Application_Model_DbTable_Users();
        $userList = $userModel->getItemsList();

        $this->view->availableLesson = $lesson;
        $this->view->userList = $userList;

    }

    public function setupAction()
    {
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

            $this->_helper->redirector('join', 'lesson');
        }
    }

    public function joinAction()
    {
        //$this->_helper->layout()->disableLayout();
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $lessonTable = new Application_Model_DbTable_Lesson();

        $result = $lessonTable->checkAvailableLesson($userId);

        if($result) {
            $broker = new Aimya_View_Helper_BaseLink();
            $baseLink = $broker->baseLink();

            $userModel = new Application_Model_DbTable_Users();
            $lessonTable = new Application_Model_DbTable_Users();
            $myStreamName = '';
            $partnerStreamName = '';
            $teacherId = '';
            $studentId = '';
            if((Zend_Auth::getInstance()->getIdentity()->role > 1)) {
                $teacher = $userModel->getItem($result['partner_id']);
                $teacherId = $result['partner_id'];
                $studentId = $result['creator_id'];
                $myStreamName = $result['creator_stream_name'];
                $partnerStreamName = $result['partner_stream_name'];
            } else {
                $teacher = $userModel->getItem($result['creator_id']);
                $teacherId = $result['creator_id'];
                $studentId = $result['partner_id'];
                $myStreamName = $result['partner_stream_name'];
                $partnerStreamName = $result['creator_stream_name'];
            }

            /*$this->view->screenType = "simple";
            if ($this->getRequest()->isXmlHttpRequest()){
                if($this->getRequest()->getParam('full') == 0){
                    //$this->_helper->layout()->disableLayout();
                    if($this->getRequest()->getParam('full') == 0) {
                        $this->view->screenType = "simple";
                    }
                }
            }*/
            $flashObj = '<object clsid:d27cdb6e-ae6d-11cf-96b8-444553540000 width="100%" height="100%" id="aimia_lesson"><param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $teacher['username'] . '&partnerId=' . $teacherId . '&userId=' . $studentId . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $result['id'] .'"><object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="100%" height="100%"><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $teacher['username'] . '&partnerId=' . $teacherId . '&userId=' . $studentId . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $result['id'] .'"><p>Either scripts and active content are not permitted to run or Adobe Flash Player version10.0.0 or greater is not installed.</p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" /></a></object></object>';

            $this->view->flashObj = $flashObj;
            $this->view->responce = $result;
            $this->view->result = true;
        } else {
            $this->view->result = false;
        }

    }

    public function detailsAction()
    {
        //$this->_helper->layout()->disableLayout();
    }

    public function uploadAction() {

        //$text = json_encode($_POST);
        //$text = session_id();
        //$this->write($text);
        $identityId = Zend_Auth::getInstance()->getIdentity()->id;

        $lessonModel = new Application_Model_Lesson();
        $lessonTable = new Application_Model_DbTable_Lesson();
        $activeLesson = $lessonTable->checkAvailableLesson($identityId);

        $presPath = $lessonModel->createPresentationPath($activeLesson['id']);

        //$this->write($presPath);

        $formData = $this->getRequest()->getParams();

        if(isset($_FILES['Filedata']['name']) && $_FILES['Filedata']['name'] != '') {
            //$lessonModel->delTree($presPath);
            $presentationForm = new Application_Form_Presentation();
            $presentationForm->getElement("Filedata")->setDestination($presPath);
            $presentationForm->Filedata->receive();

            //$fileName = $_FILES['Filedata']['name'];
            $filePath = $presPath . DIRECTORY_SEPARATOR . $formData['Filename'];

            /*$text = json_encode($filePath);
            //$text .= session_id();
            $this->write($text);*/

            exec("conv.sh {$filePath}");
            $info = pathinfo($filePath);
            $pdfName = $info['filename'] . '.pdf';

            $pdfPath = $presPath . $pdfName;

            //$this->write(' / ' . $pdfPath . " / \n");

            @mkdir($presPath . 'jpges' . DIRECTORY_SEPARATOR);
            $imgsPath = $presPath . 'jpges' . DIRECTORY_SEPARATOR;

            //$this->write(' / ' . "{$imgsPath}file.jpg" . " / \n");

            exec("convert {$pdfPath} {$imgsPath}file.jpg");

        }
        exit;

    }

    public function filesAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {

            $data = $this->getRequest()->getPost();
            if(isset($data['lesson_id'])) {
                $lessonId = $data['lesson_id'];
                $lessonModel = new Application_Model_Lesson();

                $imageArray = $lessonModel->getImages($lessonId);
                if(isset($imageArray) && !empty($imageArray)) {
                    $this->view->answer = 'success';
                    $this->view->comment = '';
                    $this->view->data  = $imageArray;
                } else {
                    $this->view->answer = 'failure';
                    $this->view->comment = 'There no any images';
                    $this->view->data  = "";
                }
            } else {
                $this->view->answer = 'failure';
                $this->view->comment = '';
                $this->view->data  = "There no any parameters";
            }
        }


    }

    public function startAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->answer = 'success';
        }
    }

    public function stopAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->answer = 'success';
        }
    }

    public function endAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            if($this->getRequest()->getParam('lesson_id')){
                $lessonId = $this->getRequest()->getParam('lesson_id');
                $lessonTable = new Application_Model_DbTable_Lesson();
                $status = $lessonTable->changeStatus($lessonId);
                if($status) {
                    $this->view->answer = 'success';
                } else {
                    $this->view->answer = 'failure';
                }

            }
        }
    }

    public function updatesizeAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            if($this->getRequest()->getParam('lesson_id')){
                $flashSize = $this->getRequest()->getParam('flash_size');
                $lessonId = $this->getRequest()->getParam('lesson_id');
                $lessonTable = new Application_Model_DbTable_Lesson();
                $status = $lessonTable->changeFlashSize($lessonId, $flashSize);

                if($status) {
                    $this->view->answer = 'success';
                } else {
                    $this->view->answer = 'failure';
                }

            }
        }
    }

    public function getsizeAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            if($this->getRequest()->getParam('lesson_id')){
                $lessonId = $this->getRequest()->getParam('lesson_id');
                $lessonTable = new Application_Model_DbTable_Lesson();
                $size = $lessonTable->getFlashSize($lessonId);

                $this->view->flashSize = $size;
            }
        }
    }

    function write($the_string )
    {
        if( $fh = @fopen("./logfile.txt", "a+") )
        {
            fputs( $fh, $the_string, strlen($the_string) );
            fclose( $fh );
            return( true );
        }
        else
        {
            return( false );
        }
    }

}

