<?php

class LessonController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this->_helper->AjaxContext()
            ->addActionContext('setup', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('join', 'json')
            ->addActionContext('upload', 'json')
            ->addActionContext('files', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout("layoutInside");
        $this->_helper->layout()->getView()->headTitle('Friends');

        $userModel = new Application_Model_DbTable_Users();
        $userList = $userModel->getItemsList();

        $this->view->userList = $userList;

    }

    public function setupAction()
    {
        //$this->_helper->layout()->disableLayout();

        //if ($this->getRequest()->isXmlHttpRequest()){
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
                /*$lastAddedId = $lessonModel->getAdapter()->lastInsertId();


                $flashObj = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="1000" height="1000" id="aimia_lesson">
                                <param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" />
                                <param name="quality" value="high" />
                                <param name="bgcolor" value="#ffffff" />
                                <param name="allowScriptAccess" value="sameDomain" />
                                <param name="allowFullScreen" value="true" />
                                <param name="flashvars" value="movie=' . $baseLink . '/flash/aimia_lesson.swf&userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $student['username'] . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $resultParams['teacherStream'] . '&partnerStreamName=' . $resultParams['studentStream'] . '&soID=' . $resultParams['soID'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $lastAddedId .'">
                                <object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="1000" height="1000">
                                    <param name="quality" value="high" />
                                    <param name="bgcolor" value="#ffffff" />
                                    <param name="allowScriptAccess" value="sameDomain" />
                                    <param name="allowFullScreen" value="true" />
                                    <param name="flashvars" value="movie=' . $baseLink . '/flash/aimia_lesson.swf&userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $student['username'] . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $resultParams['teacherStream'] . '&partnerStreamName=' . $resultParams['studentStream'] . '&soID=' . $resultParams['soID'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $lastAddedId .'">
                                    <p>
                                        Either scripts and active content are not permitted to run or Adobe Flash Player version
                                        10.0.0 or greater is not installed.
                                    </p>
                                    <a href="http://www.adobe.com/go/getflashplayer">
                                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
                                    </a>
                                </object>
                            </object>';

                $this->view->flashObj = $flashObj;*/
            }
        //}
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
            $myStreamName = '';
            $partnerStreamName = '';
            if((Zend_Auth::getInstance()->getIdentity()->role == 2)) {
                $teacher = $userModel->getItem($result['partner_id']);
                $myStreamName = $result['creator_stream_name'];
                $partnerStreamName = $result['partner_stream_name'];
            } else {
                $teacher = $userModel->getItem($result['creator_id']);
                $myStreamName = $result['partner_stream_name'];
                $partnerStreamName = $result['creator_stream_name'];
            }

            $this->view->screenType = "simple";
            if ($this->getRequest()->isXmlHttpRequest()){
                if($this->getRequest()->getParam('full')){
                    $this->_helper->layout()->disableLayout();
                    if($this->getRequest()->getParam('full') == 0) {
                        $this->view->screenType = "simple";
                    } else {
                        $this->view->screenType = "full";
                    }
                }
            }
            $flashObj = '
                <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="1000" height="1000" id="aimia_lesson">
                    <param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" />
                    <param name="quality" value="high" />
                    <param name="bgcolor" value="#ffffff" />
                    <param name="allowScriptAccess" value="sameDomain" />
                    <param name="allowFullScreen" value="true" />
                    <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $teacher['username'] . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $result['id'] .'">
                    <object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="1000" height="1000">
                        <param name="quality" value="high" />
                        <param name="bgcolor" value="#ffffff" />
                        <param name="allowScriptAccess" value="sameDomain" />
                        <param name="allowFullScreen" value="true" />
                        <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $teacher['username'] . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $result['id'] .'">
                        <p>
                            Either scripts and active content are not permitted to run or Adobe Flash Player version
                            10.0.0 or greater is not installed.
                        </p>
                        <a href="http://www.adobe.com/go/getflashplayer">
                            <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
                        </a>
                    </object>
                </object>
                ';

            $this->view->flashObj = $flashObj;
            $this->view->responce = $result;
            $this->view->result = true;
        } else {
            $this->view->result = false;
        }

    }

    public function fullAction()
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
            $teacher = $userModel->getItem($result['creator_id']);

            //$lessonModel = new Application_Model_DbTable_Lesson();
            //$lessonModel->changeStatus($result['id'], $status);

            /*<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash4/cabs/swflash.cab#version=4,0,0,0" id="name" width="800" height="800">
                         <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&myStreamName=' . $result['partner_stream_name'] . '&partnerStreamName=' . $result['creator_stream_name'] . '&soID=' . $result['so_id'] . '">
                         <embed name="name" src="' . $baseLink . '/flash/aimia_lesson.swf" quality="high" wmode="transparent" width="800" height="800" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed>
                         </object>*/
            $flashObj = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%" id="aimia_lesson">
                                <param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" />
                                <param name="quality" value="high" />
                                <param name="bgcolor" value="#ffffff" />
                                <param name="allowScriptAccess" value="sameDomain" />
                                <param name="allowFullScreen" value="true" />
                                <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $teacher['username'] . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $result['partner_stream_name'] . '&partnerStreamName=' . $result['creator_stream_name'] . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $result['id'] .'">
                                <object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="100%" height="100%">
                                    <param name="quality" value="high" />
                                    <param name="bgcolor" value="#ffffff" />
                                    <param name="allowScriptAccess" value="sameDomain" />
                                    <param name="allowFullScreen" value="true" />
                                    <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&partnerName=' . $teacher['username'] . '&userRole=' . Zend_Auth::getInstance()->getIdentity()->role . '&myStreamName=' . $result['partner_stream_name'] . '&partnerStreamName=' . $result['creator_stream_name'] . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink .'&lesson_id=' . $result['id'] .'">
                                    <p>
                                        Either scripts and active content are not permitted to run or Adobe Flash Player version
                                        10.0.0 or greater is not installed.
                                    </p>
                                    <a href="http://www.adobe.com/go/getflashplayer">
                                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
                                    </a>
                                </object>
                            </object>';

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

        }
    }

    public function stopAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {

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

