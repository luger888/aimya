<?php

class Application_Model_User
{

    public function addNewUser($array)
    {

        $user = new Application_Model_DbTable_Users();
        $profile = new Application_Model_DbTable_Profile();


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
        $mail->baseLink = "http://" . $_SERVER['HTTP_HOST'];

        if($mail->send()){

            $user->createUser($array);
            $lastId = $user->getAdapter()->lastInsertId();

            $profile->createProfile($lastId);


            @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'img');
            @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads');
            @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $lastId);
            @mkdir(realpath(APPLICATION_PATH . '/../public/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $lastId . DIRECTORY_SEPARATOR . 'avatar');

            return true;

        }else{

            die('problem with email');

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

            $data['password'] = $pass;

            #update password
            $db->recoverPass($data);

            #send e-mail
            $mail = new Ohana_Mail;
            $mail->setRecipient($data['email']);
            $mail->setTemplate(Ohana_Mail::FORGOT_PASSWORD);
            $mail->firstName = $userData['firstname'];
            $mail->lastName = $userData['lastname'];
            $mail->email = $data['email'];
            $mail->password = $data['password'];
            $mail->baseLink = "http://" . $_SERVER['HTTP_HOST'];
            $mail->send();

            return 'done';

        }else{

            return 'no e-mail in base';

        }
    }
}