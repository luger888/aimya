<?php

class LessonController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->AjaxContext()
            ->addActionContext('setup', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('join', 'json')
            ->addActionContext('upload', 'json')
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

                /*<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash4/cabs/swflash.cab#version=4,0,0,0" id="name" width="800" height="800">
                             <param name="flashvars" value="movie=' . $baseLink . '/flash/aimia_lesson.swf&userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&myStreamName=' . $resultParams['teacherStream'] . '&partnerStreamName=' . $resultParams['studentStream'] . '&soID=' . $resultParams['soID'] . '">
                             <embed name="name" src="' . $baseLink . '/flash/aimia_lesson.swf" quality="high" wmode="transparent" width="800" height="800" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed>
                             </object>*/

                $flashObj = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="800" height="800" id="aimia_lesson">
                                <param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" />
                                <param name="quality" value="high" />
                                <param name="bgcolor" value="#ffffff" />
                                <param name="allowScriptAccess" value="sameDomain" />
                                <param name="allowFullScreen" value="true" />
                                <param name="flashvars" value="movie=' . $baseLink . '/flash/aimia_lesson.swf&userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&myStreamName=' . $resultParams['teacherStream'] . '&partnerStreamName=' . $resultParams['studentStream'] . '&soID=' . $resultParams['soID'] . '&PHPSESSID=' . Zend_Session::getId() . '">
                                <object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="800" height="800">
                                    <param name="quality" value="high" />
                                    <param name="bgcolor" value="#ffffff" />
                                    <param name="allowScriptAccess" value="sameDomain" />
                                    <param name="allowFullScreen" value="true" />
                                    <param name="flashvars" value="movie=' . $baseLink . '/flash/aimia_lesson.swf&userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&myStreamName=' . $resultParams['teacherStream'] . '&partnerStreamName=' . $resultParams['studentStream'] . '&soID=' . $resultParams['soID'] . '&PHPSESSID=' . Zend_Session::getId() . '">
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

            //$userModel = new Application_Model_DbTable_Users();
            //$student = $userModel->getItem(Zend_Auth::getInstance()->getIdentity()->id);
            //$teacher = $userModel->getItem($result['creator_id']);

            //$lessonModel = new Application_Model_DbTable_Lesson();
            //$lessonModel->changeStatus($result['id'], $status);

            /*<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash4/cabs/swflash.cab#version=4,0,0,0" id="name" width="800" height="800">
                         <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&myStreamName=' . $result['partner_stream_name'] . '&partnerStreamName=' . $result['creator_stream_name'] . '&soID=' . $result['so_id'] . '">
                         <embed name="name" src="' . $baseLink . '/flash/aimia_lesson.swf" quality="high" wmode="transparent" width="800" height="800" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed>
                         </object>*/
            $flashObj = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="800" height="800" id="aimia_lesson">
                                <param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" />
                                <param name="quality" value="high" />
                                <param name="bgcolor" value="#ffffff" />
                                <param name="allowScriptAccess" value="sameDomain" />
                                <param name="allowFullScreen" value="true" />
                                <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&myStreamName=' . $result['partner_stream_name'] . '&partnerStreamName=' . $result['creator_stream_name'] . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '">
                                <object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="800" height="800">
                                    <param name="quality" value="high" />
                                    <param name="bgcolor" value="#ffffff" />
                                    <param name="allowScriptAccess" value="sameDomain" />
                                    <param name="allowFullScreen" value="true" />
                                    <param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&myStreamName=' . $result['partner_stream_name'] . '&partnerStreamName=' . $result['creator_stream_name'] . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() .'">
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

    public function uploadAction() {

        //Zend_Session::setOptions(array('strict' => 'on'));
        //session_id('tgpae3nf7oe20rbkuvnib6a4g7');

        $identityId = Zend_Auth::getInstance()->getIdentity()->id;
        //$identityId =  Zend_Auth::getInstance()->getStorage()->read()->id;
        //$identityId = Zend_Registry::get('userid');



        $lessonTable = new Application_Model_DbTable_Lesson();
        $activeLesson = $lessonTable->checkAvailableLesson($identityId);

        @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation');
        @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . $identityId);
        $presPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . $identityId . DIRECTORY_SEPARATOR . $activeLesson['id'];
        @mkdir($presPath);

        $formData = $this->getRequest()->getParams();



        if(isset($_FILES['Filedata']['name']) && $_FILES['Filedata']['name'] != '') {
            $presentationForm = new Application_Form_Presentation();
            $presentationForm->getElement("Filedata")->setDestination($presPath);
            $presentationForm->Filedata->receive();

            $filePath = $presPath . DIRECTORY_SEPARATOR . $formData['Filename'];

            exec("conv.sh {$filePath}");
            $info = pathinfo($filePath);
            $pdfPath = $info['filename'] . '.pdf';
            $imgsPath = $presPath . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

            exec("convert {$pdfPath} {$imgsPath}file.jpg");
        }
        exit;
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

