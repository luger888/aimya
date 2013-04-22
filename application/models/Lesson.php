<?php

class Application_Model_Lesson
{
    public function createFlashParams($array = array())
    {
        $result = array();
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $value = md5($value . time());
                $result[$key] = $value;
            }
            return $result;
        } else {
            return false;
        }
    }

    public function createPresentationPath($lessonId)
    {
        $identityId = Zend_Auth::getInstance()->getIdentity()->id;
        $lessonTable = new Application_Model_DbTable_Lesson();
        $activeLesson = $lessonTable->checkAvailableLesson($identityId);

        $folderModel = new Application_Model_Folder();
        $notePath = 'users/' . $activeLesson['creator_id'] . '/' . $lessonId . '/presentation/';
        $folderModel->createFolderChain($notePath, '/');
        return $notePath;
    }

    public function createNotesPath($lessonId, $teacherId)
    {
        $folderModel = new Application_Model_Folder();
        $notePath = 'users/' . $teacherId . '/' . $lessonId . '/notes/';
        $folderModel->createFolderChain($notePath, '/');
        return $notePath;
    }

    public function createVideoPath($lessonId, $teacherId)
    {

        $folderModel = new Application_Model_Folder();
        $notePath = 'users/' . $teacherId . '/' . $lessonId . '/video/';
        $folderModel->createFolderChain($notePath, '/');
        return $notePath;

    }

    public function openDisplay($lessonId)
    {

        $port = rand(4000, 4999);
        passthru("/usr/local/bin/phase1_startenv.sh $lessonId $port", $result);

        if ($result == 0) {
            return $port;
        } else {
            return false;
        }
    }

    public function openLesson($lessonId, $port)
    {

        exec("/usr/local/bin/phpscr.sh $lessonId $port > /dev/null 2>/dev/null &", $result);

        if ($result == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function closeDisplay()
    {
        $result = exec('close_display.sh');
        return $result;
    }

    public function startRecording($display, $path, $time, $teacherStream, $lessonId, $seleniumPort, $studentStream = null)
    {
        $this->write('start <br>', 'videoRec');
        $seconds = $time * 60;
        $time2 = gmdate("H:i:s", $seconds);
        exec("phase2_rec.sh $display $path $time2 $lessonId > /dev/null 2>/dev/null &");
        $res = exec("/usr/local/bin/phase2.1_rtmpdump.sh $teacherStream $path > /dev/null 2>/dev/null &");
        if($studentStream){
            $res2 = exec("/usr/local/bin/phase2.1.1_rtmpdump_stud.sh $studentStream $path > /dev/null 2>/dev/null &");

        }
       // exec("phase_killtimer.sh $lessonId $seleniumPort $lessonId $time > /dev/null 2>/dev/null &");
        $pathAudio = $path .'_audio';
        $this->write(date('Y-m-d H:i:s').' / '.$studentStream . ' ->studentStream /n'. $teacherStream . ' ->teacherSteam /n' . $time . ' -> time /n' . $lessonId . ' ->lesson_id /n'. $seleniumPort . ' ->port', 'videoRec');
        $this->write($res . ' teacherStream<br>', 'videoRec');
        $this->write('end', 'videoRec');
        $this->write("/usr/local/bin/phase2.1_rtmpdump.sh $teacherStream $pathAudio > /dev/null 2>/dev/null &");
        return true;
    }

    public function createNote($notePath, $userName, $message, $time)
    {
        $string = '<li class="note"><p class="username">' . $userName . '</p><p class="time">' . $time . '</p><p class="message">' . $message . '</p></li>';

        $fp = fopen($notePath . "notes.txt", "a");

        fwrite($fp, $string);

        fclose($fp);
    }

    public function getNotes($lessonId, $teacherId)
    {
        $notePath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . $teacherId . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'notes' . DIRECTORY_SEPARATOR . 'notes.txt';
        if (file_exists($notePath) OR is_dir($notePath)) {
            $fileContent = file_get_contents($notePath);
            return $fileContent;
        } else {
            return false;
        }


    }

    public function getVideo($teacherId, $lessonId)
    {
        $path = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . $teacherId . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'video_lesson_rec.flv';
        $pathMkv = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . $teacherId . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'video_lesson.mkv';
        if ((file_exists($path) OR is_dir($path))) {
            if (!file_exists($pathMkv) OR !is_dir($pathMkv)) {
                return 'http://aimya.com/users/'.$teacherId.'/'.$lessonId.'/video/video_lesson_rec.flv';

            } else {
                return 2;
            }
        } else {
           return false;
        }


    }

    public function getImages($lessonId)
    {
        $lessonTable = new Application_Model_DbTable_Lesson();
        $lessonData = $lessonTable->getItem($lessonId);
        $imagesPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . $lessonData['creator_id'] . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . 'jpges' . DIRECTORY_SEPARATOR;
        //$imagesPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . "1" . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'jpges' . DIRECTORY_SEPARATOR;
        if (is_dir($imagesPath)) {
            $imageNames = scandir($imagesPath);
            if ($imageNames && is_array($imageNames)) {
                $imagePath = array();
                foreach ($imageNames as $name) {
                    if (strlen($name) > 3) {
                        $search = realpath(APPLICATION_PATH . '/../public/');
                        $cutedPath = str_replace($search, "", $imagesPath);
                        $imagePath[] = $cutedPath . $name;
                        natsort($imagePath);
                        $imagePath = array_merge(array(), $imagePath);
                    }
                }

                return $imagePath;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function delTree($dir)
    {
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (substr($file, -1) == '/')
                delTree($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }

    function write($the_string, $name = 'logfile')
    {
        if ($fh = @fopen("./img/".$name.".txt", "a+")) {
            fputs($fh, $the_string, strlen($the_string));
            fclose($fh);
            return (true);
        }
        else
        {
            return (false);
        }
    }

    public function extendLesson($bookingArray = array())
    {
        $minutes = 10; //Rserve time to pay for lesson in minutes

        //payment statuses
        $paymentStatusDefault = 0;
        $paymentStatusRequested = 1;
        $paymentStatusPaid = 2;

        $reserveSeconds = $minutes * 60;
        $lessons = array(); // final lesson array to be returned
        $userTable = new Application_Model_DbTable_Users();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $bookingTable = new Application_Model_DbTable_Booking();

        //TIME FORMATTING BY TIMEZONES BLOCK
        date_default_timezone_set('Europe/London');
        $date = gmdate("m/d/Y H:i"); //current time in GMT 0
        $tzDbTable = new Application_Model_DbTable_TimeZones();
        $userGmt = $userTable->getTimeZone($identity->id); //getting time zone of logged user
        $userGmt =  $userGmt['timezone']; //exploding HH: MM by ':'
        $tz = $tzDbTable->getTimezoneByGmt($userGmt);

        $dtzone = new DateTimeZone($tz['code']);

        $dtime = new DateTime();
        $dtime->setTimeZone($dtzone);
        $time = $dtime->getOffset();

        $isTeacher = 0;
        $dateWithUTC = gmdate("m/d/Y H:i", time() + (($time))); //adding timezone to current date

        // END -- TIME FORMATTING BY TIMEZONES BLOCK
        $lessonTable = new Application_Model_DbTable_Lesson();


        foreach ($bookingArray as $lesson) { //extending base array of lessons
            $joinLesson = $lessonTable->checkAvailableLesson($lesson['userData']['id']);
            $lessonDuration = $lesson['booking']['duration'] * 60; //lesson duration in seconds
            $starting_time = strtotime($lesson['booking']['started_at']); //booking started_at time to UNIX stamp
            if ($lesson['booking']['sender_id'] != $identity->id) {
                $creator_tz = $lesson['booking']['creator_tz']; //timezone in seconds
                $starting_time = ($starting_time + $time  ) - $creator_tz ;

            }
            $currentTimeUtc = strtotime($dateWithUTC); //currentTime + UTC of user to UNIX stamp
            $timeDifference = $starting_time - $currentTimeUtc;
            if ($timeDifference <= $reserveSeconds && $timeDifference > 0) { //if difference between starting time and current is 10 minutes or less, but not less than 0
                $isOnline = 1;

                if ($bookingTable->isTeacher($lesson['booking']['id'], $identity->id)) { //if user is a teacher in current lesson
                    $isTeacher = 1;
                    if ($lesson['booking']['payment_status'] == $paymentStatusDefault) { //payment status 0
                        $lesson['booking']['sendRequest'] = 1;
                    } else if ($lesson['booking']['payment_status'] == $paymentStatusRequested) { //payment status 1
                        $lesson['booking']['pending'] = 1;
                    }
                    else if ($lesson['booking']['payment_status'] == $paymentStatusPaid) { //payment status 2
                        $lesson['booking']['paid'] = 1;
                        if ($currentTimeUtc - $starting_time <= $lessonDuration) {
                            $lesson['booking']['startLesson'] = 1;
                        }
                    }

                } else { //if user is a student in current lesson
                    $isTeacher = 0;
                    if ($joinLesson) { //if available for student to join
                        $lesson['booking']['join'] = 1;
                    }
                    if ($lesson['booking']['payment_status'] == $paymentStatusDefault) { //payment status 0
                        $lesson['booking']['waiting'] = 1;
                    } else if ($lesson['booking']['payment_status'] == $paymentStatusRequested) { //payment status 1
                        $lesson['booking']['pay'] = 1;
                    }
                    else if ($lesson['booking']['payment_status'] == $paymentStatusPaid) { //payment status 2
                        $lesson['booking']['paid'] = 1;

                    }
                }

            } else {
                if($timeDifference < 0){
                    $lesson['booking']['expired'] = 1;
                }else{

                    $lesson['booking']['waiting'] = 1;
                }


                $isOnline = 0;
            }

            $lesson['booking']['isOnline'] = $isOnline; //adding is online attr to lesson
            $lesson['booking']['isTeacher'] = $isTeacher; //adding is teacher attr to lesson
            $lesson['booking']['startingTime'] = date('m/d/Y H:i', $starting_time);
            ; //starting time with user utc
            $lessons[] = $lesson;

        }
        //END FOREACH

        return $lessons;

    }
}