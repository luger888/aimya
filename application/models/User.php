<?php

class Application_Model_User
{

    public function addNewUser($array){

        $user = new Application_Model_DbTable_Users();
        #$personal = new Application_Model_DbTable_Personal();

        if($user->checkByMail($array['email'])){

            return 0;

        }else{


            $token = md5(uniqid(mt_rand(), true));

            $array['token'] = $token;

            /*$mail = new Aimya_Mail;
            $mail->setRecipient($array['email']);
            $mail->setTemplate(Aimya_Mail::SIGNUP_ACTIVATION);
            $mail->firstName = $array['firstname'];
            $mail->lastName = $array['lastname'];
            $mail->email = $array['email'];
            $mail->password = $array['password'];
            $mail->token = $token;
            $mail->baseLink = "http://" . $_SERVER['HTTP_HOST'];

            if($mail->send()){*/

                $user->createUser($array);


                /*return 1;

            }else{

                return 2;

            }*/

        }
    }
}