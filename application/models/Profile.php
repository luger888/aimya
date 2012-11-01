<?php

class Application_Model_Profile
{
    public function getProfileAccount($user_id)
    {
        $profileModel = new Application_Model_DbTable_Profile();
        $userModel = new Application_Model_DbTable_Users();

        $profile = $profileModel->getProfile($user_id);
        $user = $userModel->getUser($user_id);
        $accountUserArray = array_merge($profile, $user);  //merge data from user and account db-tables
        return $accountUserArray;
    }
}