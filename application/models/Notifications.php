<?php

class Application_Model_Notifications
{

    public function array_searchRecursive($needle, $haystack, $strict = false, $path = array())
    {

        foreach ($haystack as $key => $val) {
            if (is_array($val) && $subPath = $this->array_searchRecursive($needle, $val, $strict, $path)) {
                $path = array_merge($path, array($key), $subPath);
                return $path;
            } elseif ((!$strict && $val == $needle) || ($strict && $val === $needle)) {
                $path[] = $key;
                return $path;
            }
        }
        return false;
    }

    public function sendAlerts($user_id, $alert = null)
    {
        $notesDb = new Application_Model_DbTable_Notifications();

        $notes = $notesDb->getNotifications($user_id);

        if ($alert) {
            $result = $this->array_searchRecursive($alert, $notes);

            if ($result) {

                switch ($alert) {
                    case 'friend':
                        $message = 'You got new friend request on Aimya.com!';
                        break;
                    case 'message':
                        $message = 'You got new message on Aimya.com!';
                        break;
                    case 1 || 2 || 3 || 4 || 5:
                        $message = 'You got new review on your lesson with rating of ' . $alert . ' on Aimya.com!';
                        break;
                }

                    $userDb = new Application_Model_DbTable_Users();
                    $userInfo = $userDb->getUser($user_id);
                    $mail = new Aimya_Mail;
                    $mail->setRecipient($userInfo['email']);
                    $mail->setTemplate(Aimya_Mail::ALERT);
                    $mail->firstname = $userInfo['firstname'];
                    $mail->lastname = $userInfo['lastname'];
                    $mail->message = $message;
                    $resultSend = $mail->send();

                    Zend_Debug::dump('send resuklt : ' .$resultSend);
                Zend_Debug::dump('current alert : '.$alert .' <br>');
                Zend_Debug::dump('result for occurences : '.$result .' <br>');
                Zend_Debug::dump('message: '.$message .' <br>');
                Zend_Debug::dump($userInfo );
                    die;


            }

        }
        return false;
    }

}