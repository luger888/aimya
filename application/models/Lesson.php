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
        @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation');
        @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . $identityId);
        $presPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . $identityId . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR;
        @mkdir($presPath);

        //$this->write(' / ' . $identityId . " / \n");

        return $presPath;
    }

    public function getImages($lessonId)
    {
        $lessonTable = new Application_Model_DbTable_Lesson();
        $lessonData = $lessonTable->getItem($lessonId);
        $imagesPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . $lessonData['creator_id'] . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'jpges' . DIRECTORY_SEPARATOR;
        //$imagesPath = realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'presentation' . DIRECTORY_SEPARATOR . "1" . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'jpges' . DIRECTORY_SEPARATOR;
        $imageNames = scandir($imagesPath);
        $imagePath = array();
        foreach ($imageNames as $name) {
            if (strlen($name) > 3) {
                $search = realpath(APPLICATION_PATH . '/../public/');
                $cutedPath = str_replace($search, "", $imagesPath);
                $imagePath[] = $cutedPath . $name;
            }
        }

        return $imagePath;
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

    function write($the_string)
    {
        if ($fh = @fopen("./logfile.txt", "a+")) {
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
        $minutes = 10;//Rserve time to pay for lesson in minutes

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

        $userGmt = $userTable->getTimeZone($identity->id); //getting time zone of logged user
        $separatedData = explode(':', $userGmt['timezone']); //exploding HH: MM by ':'
        $minutesInHours = $separatedData[0] * 60; // HH -> minutes
        $minutesInDecimals = $separatedData[1]; // MM -> minutes
        $totalMinutes = $minutesInHours + $minutesInDecimals; //converted timezone to minutes
        $isTeacher = 0;
        $dateWithUTC = gmdate("m/d/Y H:i", strtotime($date) + (($totalMinutes) * 60)); //adding timezone to current date
        // END -- TIME FORMATTING BY TIMEZONES BLOCK


        foreach ($bookingArray as $lesson) { //extending base array of lessons
            $lessonDuration = $lesson['booking']['duration'] *60; //lesson duration in seconds
            $starting_time = strtotime($lesson['booking']['started_at']); //booking started_at time to UNIX stamp
            $currentTimeUtc = strtotime($dateWithUTC); //currentTime + UTC of user to UNIX stamp
            $timeDifference = $starting_time - $currentTimeUtc;
            if ($timeDifference <= $reserveSeconds && $timeDifference > 0){ //if difference between current time and starting is 10 minutes or less, but not less than 0
                $isOnline = 1;

                if ($bookingTable->isTeacher($lesson['booking']['id'], $identity->id)){//if user is a teacher in current lesson
                    $isTeacher = 1;
                    if($lesson['booking']['payment_status'] == $paymentStatusDefault){//payment status 0
                        $lesson['booking']['sendRequest'] = 1;
                    }else if($lesson['booking']['payment_status'] == $paymentStatusRequested){//payment status 1
                        $lesson['booking']['pending']  = 1 ;
                    }
                    else if($lesson['booking']['payment_status'] == $paymentStatusPaid){//payment status 2
                        $lesson['booking']['paid'] = 1;
                        if($currentTimeUtc - $starting_time <=$lessonDuration){
                            $lesson['booking']['startLesson'] = 1;
                        }
                    }

                }else{ //if user is a student in current lesson
                    $isTeacher = 0;

                    if($lesson['booking']['payment_status'] == $paymentStatusDefault){//payment status 0
                        $lesson['booking']['waiting'] = 1;
                    }else if($lesson['booking']['payment_status'] == $paymentStatusRequested){//payment status 1
                        $lesson['booking']['pay']  = 1 ;
                    }
                    else if($lesson['booking']['payment_status'] == $paymentStatusPaid){//payment status 2
                        $lesson['booking']['paid'] = 1;

                    }
                }

            }else{
                $lesson['booking']['waiting'] = 1;
                $isOnline = 0;
            }
            $lesson['booking']['isOnline'] = $isOnline;//adding is online attr to lesson
            $lesson['booking']['isTeacher']  = $isTeacher;//adding is teacher attr to lesson
            $lesson['booking']['debug'] = '<br>НАчало : ' .($lesson['booking']['started_at']) . ' - ВРемя резерва : ' . $dateWithUTC . ' - current date :' . $date . 'usergmt :' . $userGmt['timezone'];

            $lessons[] = $lesson;

        }//END FOREACH

        return $lessons;

    }
}