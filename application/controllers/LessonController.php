<?php

class LessonController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("layoutInner");
        $this->_helper->AjaxContext()
            ->addActionContext('setup', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('join', 'json')
            ->addActionContext('upload', 'json')
            ->addActionContext('files', 'json')
            ->addActionContext('end', 'json')
            ->addActionContext('recording', 'json')
            ->addActionContext('updatesize', 'json')
            ->addActionContext('getsize', 'json')
            ->addActionContext('notes', 'json')
            ->addActionContext('correspondence', 'json')
            ->addActionContext('pay', 'json')
            ->addActionContext('review', 'json')
            ->addActionContext('video', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {

        $this->_helper->layout()->getView()->headTitle('Lessons');

        $this->view->userId = $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $lessonTable = new Application_Model_DbTable_Lesson();

        $lesson = $lessonTable->checkAvailableLesson($userId);
        $lessonModel = new Application_Model_Lesson();
        $bookingTable = new Application_Model_DbTable_Booking();
        $bookingList = $bookingTable->getFullBookingData($userId);
        $extendedBookingList = $lessonModel->extendLesson($bookingList);
        $this->view->availableLesson = $lesson;
        $this->view->bookingList = $extendedBookingList;


    }

    public function setupAction()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $studentId = $this->getRequest()->getParam('student_id');
        $bookingId = $this->getRequest()->getParam('booking_id');
        if (isset($studentId) && isset($bookingId)) {
            $lessonTable = new Application_Model_DbTable_Lesson();
            $isAvailable = $lessonTable->checkAvailableLesson($userId);
            if (!$isAvailable) {


                $broker = new Aimya_View_Helper_BaseLink();
                $baseLink = $broker->baseLink();

                $userModel = new Application_Model_DbTable_Users();
                $student = $userModel->getItem($studentId);
                $teacher = $userModel->getItem(Zend_Auth::getInstance()->getIdentity()->id);
                $lessonModel = new Application_Model_Lesson();
                $params = array(
                    'teacherStream' => $teacher['username'],
                    'studentStream' => $student['username'],
                    'soID' => $teacher['id'] . $student['id'],
                );

                $resultParams = $lessonModel->createFlashParams($params);

                $dataToInsert = array(
                    'creator_id' => $teacher['id'],
                    'partner_id' => $student['id'],
                    'creator_stream_name' => $resultParams['teacherStream'],
                    'partner_stream_name' => $resultParams['studentStream'],
                    'so_id' => $resultParams['soID'],
                    'booking_id' => $bookingId,
                    'status' => 1,
                );


                $bookingTable = new Application_Model_DbTable_Booking();
                $booking = $bookingTable->getItem($bookingId);

                $res = $lessonTable->startLesson($dataToInsert);
                if ($booking['feedback']) {
                    $lessonFeadbackTable = new Application_Model_DbTable_LessonFeedback();
                    $lessonFeadbackTable->createDefaultFeedback($res, Zend_Auth::getInstance()->getIdentity()->id);
                }
                if ($booking['video']) {
                    $lessonTable = new Application_Model_DbTable_Lesson();
                    $activeLesson = $lessonTable->checkAvailableLesson(Zend_Auth::getInstance()->getIdentity()->id);

                    $videoPath = $lessonModel->createVideoPath($res, $activeLesson['creator_id']);

                    $openDispay = $lessonModel->openDisplay($activeLesson['id']);
                    sleep(8);
                    if ($openDispay !== FALSE) {
                        $res = $lessonTable->setSeleniumPort($activeLesson['id'], $openDispay);

                        if ($res) {
                            $lessonModel->openLesson($activeLesson['id'], $openDispay);

                            $stream = getHostByName(getHostName()) . '/oflaDemo/' . $resultParams['teacherStream'];
                            $studentStream = getHostByName(getHostName()) . '/oflaDemo/' . $resultParams['studentStream'];
                            $lessonModel->startRecording($activeLesson['id'], $videoPath . 'video_lesson', $booking['duration'], $stream, $activeLesson['id'], $openDispay, $studentStream);
                        }
                    }

                }

                $this->_helper->redirector('join', 'lesson');
            }
        }
    }

    public function joinAction()
    {
        //$this->_helper->layout()->disableLayout();
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $lessonTable = new Application_Model_DbTable_Lesson();
        $this->view->lessonStatus = 1;
        $result = $lessonTable->checkAvailableLesson($userId);

        if ($result) {
            $broker = new Aimya_View_Helper_BaseLink();
            $baseLink = $broker->baseLink();

            $userModel = new Application_Model_DbTable_Users();
            $timeZone = $userModel->getTimeZone();
            $tzDbTable = new Application_Model_DbTable_TimeZones();
            $timeZone =  $tzDbTable->getItem($timeZone['timezone']);
            $lessonTable = new Application_Model_DbTable_Users();
            $userRole = '';
            $myStreamName = '';
            $partnerStreamName = '';
            $teacherId = '';
            $studentId = '';
            $bookingTable = new Application_Model_DbTable_Booking();
            if (!$bookingTable->isTeacher($result['booking_id'], $userId)) {
                $teacher = $userModel->getItem($result['creator_id']);
                $teacherName = $teacher['username'];
                $teacherId = $result['creator_id'];
                $studentId = $result['partner_id'];
                $myStreamName = $result['partner_stream_name'];
                $fsMode = $result['partner_flash_size'];
                $partnerStreamName = $result['creator_stream_name'];
            } else {
                $teacher = $userModel->getItem($result['partner_id']);
                $teacherName = $teacher['username'];
                $teacherId = $result['partner_id'];
                $studentId = $result['creator_id'];
                $myStreamName = $result['creator_stream_name'];
                $fsMode = $result['creator_flash_size'];
                $partnerStreamName = $result['partner_stream_name'];
            }

            $booking = $bookingTable->getItem($result['booking_id']);

            if ($bookingTable->isTeacher($result['booking_id'], $userId)) {
                $userRole = '2';
            } else {
                $userRole = '1';
            }

            $flashObj = '<object clsid:d27cdb6e-ae6d-11cf-96b8-444553540000 width="100%" height="100%" id="aimia_lesson"><param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&rtmp=rtmp://184.169.133.140/oflaDemo&text_xml_url=/flash/lessontexts.xml&partnerName=' . $teacherName . '&partnerId=' . $teacherId . '&userId=' . $studentId . '&userRole=' . $userRole . '&userTZ=' . $timeZone['gmt'] . '&total_time=' . $booking['duration'] * 60 . '&focus_name=' . addslashes($booking['focus_name']) . '&fs_mode=' . $fsMode . '&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&lang=' . Zend_Controller_Front::getInstance()->getBaseUrl() . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink . '&lesson_id=' . $result['id'] . '&booking_id=' . $result['booking_id'] . '"><object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="100%" height="100%"><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="userName=' . Zend_Auth::getInstance()->getIdentity()->username . '&rtmp=rtmp://184.169.133.140/oflaDemo&text_xml_url=/flash/lessontexts.xml&partnerName=' . $teacherName . '&partnerId=' . $teacherId . '&userId=' . $studentId . '&userRole=' . $userRole . '&userTZ=' . $timeZone['gmt'] . '&total_time=' . $booking['duration'] * 60 . '&focus_name=' . addslashes($booking['focus_name']) . '&fs_mode=' . $fsMode . '&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&lang=' . Zend_Controller_Front::getInstance()->getBaseUrl() . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink . '&lesson_id=' . $result['id'] . '&booking_id=' . $result['booking_id'] . '"><p>Either scripts and active content are not permitted to run or Adobe Flash Player version10.0.0 or greater is not installed.</p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" /></a></object></object>';

            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->view->flashObj = $flashObj;
            } else {
                $this->view->flashObj = $flashObj;
                $this->view->responce = $result;
                $this->view->result = true;
            }

        } else {
            $this->view->result = false;
        }

    }

    public function recordingAction()
    {
        if ($this->getRequest()->getParam('lessonId')) {
            //$userId = $this->getRequest()->getParam('partner_id');
            $lessonTable = new Application_Model_DbTable_Lesson();
            //$this->view->lessonStatus = 1;
            $result = $lessonTable->getItem($this->getRequest()->getParam('lessonId'));

            if ($result) {
                $broker = new Aimya_View_Helper_BaseLink();
                $baseLink = $broker->baseLink();

                $userModel = new Application_Model_DbTable_Users();
                $timeZone = $userModel->getTimeZone();
                $tzDbTable = new Application_Model_DbTable_TimeZones();
                $timeZone =  $tzDbTable->getItem($timeZone['timezone']);
                $bookingTable = new Application_Model_DbTable_Booking();

                $teacher = $userModel->getItem($result['creator_id']);
                $student = $userModel->getItem($result['partner_id']);
                $teacherName = $teacher['username'];
                $studentName = $student['username'];
                $teacherId = $result['creator_id'];
                $studentId = $result['partner_id'];
                $myStreamName = $result['partner_stream_name'];
                $fsMode = $result['partner_flash_size'];
                $partnerStreamName = $result['creator_stream_name'];

                $booking = $bookingTable->getItem($result['booking_id']);
                $userRole = '3';

                $flashObj = '<object clsid:d27cdb6e-ae6d-11cf-96b8-444553540000 width="100%" height="100%" id="aimia_lesson"><param name="movie" value="' . $baseLink . '/flash/aimia_lesson.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="userName=' . $studentName . '&rtmp=rtmp://184.169.133.140/oflaDemo&text_xml_url=/flash/lessontexts.xml&partnerName=' . $teacherName . '&partnerId=' . $teacherId . '&userId=' . $studentId . '&userRole=3&userTZ=' . $timeZone['gmt'] . '&total_time=' . $booking['duration'] * 60 . '&focus_name=' . addslashes($booking['focus_name']) . '&fs_mode=1&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&lang=' . Zend_Controller_Front::getInstance()->getBaseUrl() . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink . '&lesson_id=' . $result['id'] . '&booking_id=' . $result['booking_id'] . '"><object type="application/x-shockwave-flash" data="' . $baseLink . '/flash/aimia_lesson.swf" width="100%" height="100%"><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="userName=' . $studentName . '&rtmp=rtmp://184.169.133.140/oflaDemo&text_xml_url=/flash/lessontexts.xml&partnerName=' . $teacherName . '&partnerId=' . $teacherId . '&userId=' . $studentId . '&userRole=' . $userRole . '&userTZ=' . $timeZone['gmt'] . '&total_time=' . $booking['duration'] * 60 . '&focus_name=' . addslashes($booking['focus_name']) . '&fs_mode=' . $fsMode . '&myStreamName=' . $myStreamName . '&partnerStreamName=' . $partnerStreamName . '&soID=' . $result['so_id'] . '&PHPSESSID=' . Zend_Session::getId() . '&domain=' . $baseLink . '&lesson_id=' . $result['id'] . '&booking_id=' . $result['booking_id'] . '"><p>Either scripts and active content are not permitted to run or Adobe Flash Player version10.0.0 or greater is not installed.</p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" /></a></object></object>';


                $this->view->flashObj = $flashObj;
                $this->view->responce = $result;
                $this->view->result = true;


            } else {
                $this->view->result = false;
            }
        } else {
            die('server error');
        }

    }

    public function tempAction()
    {
        $lessonModel = new Application_Model_Lesson();
        $lessonTable = new Application_Model_DbTable_Lesson();
        $activeLesson = $lessonTable->getItem($this->getRequest()->getParam('display_id'));

        $videoPath = $lessonModel->createVideoPath($activeLesson['id'], $activeLesson['creator_id']);

        $openDispay = $lessonModel->openDisplay($activeLesson['id']);
        sleep(5);

        //$this->write($openDispay);

        if ($openDispay !== FALSE) {

            //$res = $lessonTable->setSeleniumPort($activeLesson['id'], $openDispay);

            //if ($res) {
            $lessonModel->openLesson($activeLesson['id'], $openDispay);
            //$lessonModel->startRecording($activeLesson['id'], $videoPath . 'video_lesson', 5);
            //}
        }


    }

    public function detailsAction()
    {
        //$this->_helper->layout()->disableLayout();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $reviewDb = new Application_Model_DbTable_Review();
        $lessonTable = new Application_Model_DbTable_Lesson();
        $studentLessons = $lessonTable->getStudentLessons();
        $filterForm = new Application_Form_LessonFilter();
        $feedbackForm = new Application_Form_Feedback();
        $feedbackTable = new Application_Model_DbTable_LessonFeedback();
        $bookingTable = new Application_Model_DbTable_Booking();
        if ($this->getRequest()->isPost()) {
            $this->view->filterInfo = '<div class="floatRight">From: ' . $this->getRequest()->getParam('fromPeriod') . ' To: ' . $this->getRequest()->getParam('toPeriod') . '</div>';
            $this->view->studentLessons = $lessonTable->getStudentLessons($this->getRequest()->getParam('fromPeriod'), $this->getRequest()->getParam('toPeriod'));
        } else {
            $this->view->studentLessons = $lessonTable->getStudentLessons();

        }
        $this->view->feedbackForm = $feedbackForm;
        $this->view->filterForm = $filterForm;
        //$this->view->studentLessons = $studentLessons;
        $this->view->review = new Application_Model_DbTable_Review();
        $this->view->feedbackTable = new $feedbackTable;
        $this->view->bookingTable = new $bookingTable;

    }

    public function uploadAction()
    {

        $identityId = Zend_Auth::getInstance()->getIdentity()->id;

        $lessonModel = new Application_Model_Lesson();
        $lessonTable = new Application_Model_DbTable_Lesson();
        $activeLesson = $lessonTable->checkAvailableLesson($identityId);

        $presPath = $lessonModel->createPresentationPath($activeLesson['id']);
        $presPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . $presPath;

        //$this->write($presPath);

        $formData = $this->getRequest()->getParams();

        if (isset($_FILES['Filedata']['name']) && $_FILES['Filedata']['name'] != '') {
            //$lessonModel->delTree($presPath);
            $presentationForm = new Application_Form_Presentation();

            $originalFilename = pathinfo($presentationForm->Filedata->getFileName());
            $newFilename = 'pres-' . uniqid() . '.' . $originalFilename['extension'];

            $presentationForm->Filedata->addFilter('Rename', $newFilename);
            $presentationForm->getElement("Filedata")->setDestination($presPath);
            $presentationForm->Filedata->receive();

            //$fileName = $_FILES['Filedata']['name'];
            $filePath = $presPath . DIRECTORY_SEPARATOR . $newFilename;

            /*$text = json_encode($filePath);
            //$text .= session_id();
            $this->write($text);*/

            exec("sudo /usr/local/bin/conv.sh $filePath", $convResult);
            //$this->write($convResult);
            //$this->write($filePath);

            $info = pathinfo($filePath);
            $pdfName = $info['filename'] . '.pdf';

            $pdfPath = $presPath . $pdfName;

            //$this->write(' / ' . $pdfPath . " / \n");
            if (!file_exists($presPath . 'jpges' . DIRECTORY_SEPARATOR) OR !is_dir($presPath . 'jpges' . DIRECTORY_SEPARATOR)) {
                mkdir($presPath . 'jpges' . DIRECTORY_SEPARATOR);
            }
            $imgsPath = $presPath . 'jpges' . DIRECTORY_SEPARATOR;

            $files = glob($imgsPath . '*'); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file))
                    unlink($file); // delete file
            }

            //$this->write(' / ' . "{$imgsPath}file.jpg" . " / \n");

            exec("convert {$pdfPath} {$imgsPath}file.jpg");

        }
        exit;

    }

    public function filesAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {

            $data = $this->getRequest()->getPost();
            if (isset($data['lesson_id'])) {
                $lessonId = $data['lesson_id'];
                $lessonModel = new Application_Model_Lesson();

                $imageArray = $lessonModel->getImages($lessonId);
                if ($imageArray && !empty($imageArray)) {
                    $this->view->answer = 'success';
                    $this->view->comment = '';
                    $this->view->data = $imageArray;
                } else {
                    $this->view->answer = 'failure';
                    $this->view->comment = 'There no any images';
                    $this->view->data = "";
                }
            } else {
                $this->view->answer = 'failure';
                $this->view->comment = '';
                $this->view->data = "There no any parameters";
            }
        }


    }

    public function startAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->answer = 'success';
        }
    }

    public function stopAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->answer = 'success';
        }
    }

    public function endAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->getParam('lesson_id') && $this->getRequest()->getParam('booking_id')) {
                $lessonId = $this->getRequest()->getParam('lesson_id');
                $bookingId = $this->getRequest()->getParam('booking_id');
                $lessonTable = new Application_Model_DbTable_Lesson();
                $bookingTable = new Application_Model_DbTable_Booking();
                $bookingStatus = $bookingTable->changeStatus($bookingId);
                $booking = $bookingTable->getItem($bookingId);
                if ($booking['video']) {

                    $lesson = $lessonTable->getItem($lessonId);
                    $seleniumPort = $lesson['selenium_port'];
                    $lessonId = $lesson['id'];

                    exec("sudo /usr/local/bin/phase3_kill.sh $lessonId $seleniumPort $lessonId");

                }
                $status = $lessonTable->changeStatus($lessonId);
                if ($status && $bookingStatus) {
                    $this->view->answer = 'success';
                } else {
                    $this->view->answer = 'failure';
                    $this->writeLog('Failure:' . $status . $bookingStatus);
                }

            }
        }
    }

    public function writeLog($data)
    {

        if ($fh = @fopen("./img/lessonLog.txt", "a+")) {

            $data = print_r($data, 1);
            fwrite($fh, $data);
            fclose($fh);
            return (true);
        } else {
            return (false);
        }

    }

    public function updatesizeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->getParam('lesson_id')) {
                $flashSize = $this->getRequest()->getParam('flash_size');
                $lessonId = $this->getRequest()->getParam('lesson_id');
                $lessonTable = new Application_Model_DbTable_Lesson();
                $status = $lessonTable->changeFlashSize($lessonId, $flashSize);

                if ($status) {
                    $this->view->answer = 'success';
                } else {
                    $this->view->answer = 'failure';
                }

            }
        }
    }

    public function getsizeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->getParam('lesson_id')) {
                $lessonId = $this->getRequest()->getParam('lesson_id');
                $lessonTable = new Application_Model_DbTable_Lesson();
                $size = $lessonTable->getFlashSize($lessonId);

                $this->view->flashSize = $size;
            }
        }
    }

    public function notesAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest()) {

            $identityId = Zend_Auth::getInstance()->getIdentity()->id;

            $bookingTable = new Application_Model_DbTable_Booking();

            $lessonModel = new Application_Model_Lesson();
            $lessonTable = new Application_Model_DbTable_Lesson();
            $activeLesson = $lessonTable->checkAvailableLesson($identityId);
            $booking = $bookingTable->getItem($activeLesson['booking_id']);
            if ($booking['notes']) {
                if ($this->getRequest()->getParam('date') && $this->getRequest()->getParam('name') && $this->getRequest()->getParam('message')) {
                    $userName = $this->getRequest()->getParam('name');
                    $message = $this->getRequest()->getParam('message');
                    $time = $this->getRequest()->getParam('date');
                    $notesPath = $lessonModel->createNotesPath($this->getRequest()->getParam('lesson_id'), $activeLesson['creator_id']);
                    $lessonModel->createNote($notesPath, $userName, $message, $time);
                    $this->view->answer = 'success';
                } else {
                    $this->view->answer = 'error';
                    $this->view->data = 'problem with parameters';
                }
            } else {
                $this->view->answer = 'error';
                $this->view->data = 'student doesn\'t pay for notes saving';
            }
        }
    }

    public function videoAction()
    {
        $lessonId = $this->getRequest()->getParam('lesson_id');
        if ($lessonId) {

            $lessonTable = new Application_Model_DbTable_Lesson();
            $reviewTable = new Application_Model_DbTable_Review();
            $lessonModel = new Application_Model_Lesson();
            $lesson = $lessonTable->getLessonByUser($lessonId);
            $identityId = Zend_Auth::getInstance()->getIdentity()->id;
            //$fileContent = $lessonModel->getNotes($lessonId, $lesson['creator_id']);
            $review = $reviewTable->getReviews($lessonId);

            $now = time(); // or your date as well
            $your_date = strtotime($lesson['created_at']);
            $datediff = $your_date - $now;
            $reviewDate = floor($datediff / (60 * 60 * 24) + 10);
            $this->view->review = $review['review'];
            $identity = Zend_Auth::getInstance()->getStorage()->read();
            $bookingDb = new Application_Model_DbTable_Booking();
            $isTeacher = $bookingDb->isTeacher($lesson['booking_id'], $identity->id);
            if ($isTeacher) {
                $this->view->isTeacher = $isTeacher;
            }
            $videoPath = $lessonModel->getVideo($lesson['creator_id'], $lesson['id']);
            if ($videoPath) {
                $this->view->videoPath = $videoPath;
            }
            $this->view->rate = $review['rating'];
            $this->view->date = $reviewDate;
            //$this->view->notes = $fileContent;


        }

    }

    public function correspondenceAction()
    {
        $lessonId = $this->getRequest()->getParam('lesson_id');
        if ($lessonId) {
            $lessonTable = new Application_Model_DbTable_Lesson();
            $reviewTable = new Application_Model_DbTable_Review();
            $lessonModel = new Application_Model_Lesson();
            $lesson = $lessonTable->getLessonByUser($lessonId);

            $fileContent = $lessonModel->getNotes($lessonId, $lesson['creator_id']);
            $review = $reviewTable->getReviews($lessonId);

            $now = time(); // or your date as well
            $your_date = strtotime($lesson['created_at']);
            $datediff = $your_date - $now;
            $reviewDate = floor($datediff / (60 * 60 * 24) + 10);
            $this->view->review = $review['review'];
            $identity = Zend_Auth::getInstance()->getStorage()->read();
            $bookingDb = new Application_Model_DbTable_Booking();
            $isTeacher = $bookingDb->isTeacher($lesson['booking_id'], $identity->id);
            if ($isTeacher) {
                $this->view->isTeacher = $isTeacher;
            }
            $this->view->rate = $review['rating'];
            $this->view->date = $reviewDate;
            if ($fileContent) {
                $this->view->notes = $fileContent;
            }

        }


    }

    function write($the_string)
    {
        if ($fh = @fopen("./img/logfile.txt", "a+")) {
            fputs($fh, $the_string, strlen($the_string));
            fclose($fh);
            return (true);
        } else {
            return (false);
        }
    }

    public function payAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $url = $this->getRequest()->getParam('url');
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->getParam('friend_id')) {
                $friendId = $this->getRequest()->getParam('friend_id');
                $bookingTable = new Application_Model_DbTable_Booking();
                $result = $bookingTable->paymentStatus($this->getRequest()->getParam('booking_id'), 1);
                if ($result) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Request successfully sent'));
                    $this->redirect($url);
                } else {
                    $this->_helper->flashMessenger->addMessage(array('failure' => 'Problem with sending request, please try again later'));
                    $this->redirect($url);
                }

            }
        }
    }

    public function reviewAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            if ($this->getRequest()->getParam('rating')) {
                $rating = $this->getRequest()->getParam('rating');
                $reviewId = $this->getRequest()->getParam('review');
                $lessonId = $this->getRequest()->getParam('lesson_id');
                $reviewTable = new Application_Model_DbTable_Review();
                $review = $reviewTable->getReviews($lessonId);
                if (!$review) {
                    $lessonTable = new Application_Model_DbTable_Lesson();
                    $lesson = $lessonTable->getItem($lessonId);
                    $bookingDb = new Application_Model_DbTable_Booking();
                    $isTeacher = $bookingDb->isTeacher($lesson['booking_id'], $userId);
                    if (!$isTeacher) {
                        $result = $reviewTable->createReview($rating, $reviewId, $lessonId, $userId, $lesson['booking_id'], $lesson['creator_id']);
                        if ($result) {
                            $notesDb = new Application_Model_Notifications();
                            $notesDb->sendAlerts($lesson['creator_id'], $rating); //sending email if needed
                            $this->view->success = 1;
                        }
                    }

                } else {
                    echo 'already reviewed';
                }
            }
        }
    }

}
