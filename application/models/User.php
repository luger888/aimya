<?php

class Application_Model_User
{

    public function addNewUser($array)
    {

        $user = new Application_Model_DbTable_Users();

        if ($user->checkByMail($array['email'])) { //need to check username also!!!!!!!!!!!!!!

            return 0;

        } else {

            $token = md5(uniqid(mt_rand(), true));
            $array['token'] = $token;
            $user->createUser($array);

        }
    }
}