<?php

class Application_Model_User
{

    public function addNewUser($array)
    {

        $user = new Application_Model_DbTable_Users();
        $profile = new Application_Model_DbTable_Profile();
        $dbAvailability = new Application_Model_DbTable_Availability();
        $dbNotifications  = new Application_Model_DbTable_Notifications();
        $token = md5(uniqid(mt_rand(), true));

        $array['token'] = $token;

        $mail = new Aimya_Mail;
        $mail->setRecipient($array['email']);
        $mail->setTemplate(Aimya_Mail::SIGNUP_ACTIVATION);
        $mail->firstname = $array['firstname'];
        $mail->lastname = $array['lastname'];
        $mail->email = $array['email'];
        $mail->password = $array['password'];
        $mail->token = $token;
        $mail->baseLink = "http://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();

        if($mail->send()){

            $user->createUser($array);
            $lastId = $user->getAdapter()->lastInsertId();

            $profile->createProfile($lastId);
            $onlineUser = new Application_Model_DbTable_OnlineUsers();
            $onlineUser->createLine($lastId);
            $dbAvailability->createAvailability($lastId);
            $dbNotifications->createNotifications($lastId);

            $folderModel = new Application_Model_Folder();
            $basePath = '/img/uploads/' . $lastId . '/avatar/base/';
            $mediumPath = '/img/uploads/' . $lastId . '/avatar/medium/';
            $thumbnailPath = '/img/uploads/' . $lastId . '/avatar/thumbnail/';
            $folderModel->createFolderChain($basePath, '/');
            $folderModel->createFolderChain($mediumPath, '/');
            $folderModel->createFolderChain($thumbnailPath, '/');

            return true;

        }else{

            return false;

        }

    }

    public function approveUser($data){

        $db = new Application_Model_DbTable_Users();
        $id = $db->checkByMail($data['query_id']);
        if($id){

            if($db->approve($data['query_id'], $data['query_key'])){

                #done
                return 'done';

            }else{

                #wrong e-mail - token
                return 'wrong e-mail - token';

            }

        }else{

            #no e-mail in base
            return 'no e-mail in base';

        }

    }

    public function passRecovery($data){

        $db = new Application_Model_DbTable_Users();
        #get user data
        $userData = $db->checkByMail($data);

        if($userData['id']){

            #create new password
            $pass = '';
            srand((double)microtime()*1000000);
            $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $char_list .= "abcdefghijklmnopqrstuvwxyz";
            $char_list .= "1234567890";

            for($i = 0; $i < 8; $i++){

                $pass .= substr($char_list,(rand()%(strlen($char_list))), 1);
            }

            $userData['password'] = $pass;

            #update password


            #send e-mail
            $mail = new Aimya_Mail;
            $mail->setRecipient($userData['email']);
            $mail->setTemplate(Aimya_Mail::FORGOT_PASSWORD);
            $mail->firstName = $userData['firstname'];
            $mail->lastName = $userData['lastname'];
            $mail->email = $userData['email'];
            $mail->password = $userData['password'];
            $mail->baseLink = "http://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();

            $result = false;
            if($mail->send()){
                $result = $db->recoverPass($userData);
            }

            return $result;

        }else{

            return 2;

        }
    }
}