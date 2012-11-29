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

    public function getAvatarPath($user_id, $imageType = 'base')
    {
        $profileModel = new Application_Model_DbTable_Profile();
        $profile = $profileModel->getProfile($user_id);
        $avatarName = $profile['avatar'];
        $avatarPath = '/img/uploads/'.$user_id.'/avatar/'. $imageType . '/' .$avatarName;

        return $avatarPath;//path for avatar
    }

    public function getFriends($user_id)
    {
        $dbUserRelations = new Application_Model_DbTable_UserRelations();
        $userRelations = $dbUserRelations->getUserRelations($user_id);
        $UserModel = new Application_Model_DbTable_Users();
        $friends = array();
        foreach($userRelations as $value){

           $friends[]= $UserModel->getUserInfo($value['friend_id']);
        }
        return $friends;//get friends firstname, lastname,username, id

    }


}