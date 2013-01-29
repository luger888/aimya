<?php

class Application_Model_Profile
{
    public function getProfileAccount($user_id)
    {
        $profileModel = new Application_Model_DbTable_Profile();
        $userModel = new Application_Model_DbTable_Users();

        $profile = $profileModel->getProfile($user_id);
        $user = $userModel->getUser($user_id);
        $accountUserArray = array_merge($profile, $user); //merge data from user and account db-tables
        return $accountUserArray;
    }

    public function getAvatarPath($user_id, $imageType = 'base')
    {
        $profileModel = new Application_Model_DbTable_Profile();
        $userModel = new Application_Model_DbTable_Users();
        $profile = $profileModel->getProfile($user_id);
        $user = $userModel->getUser($user_id);
        $avatarName = $profile['avatar'];
        if ($avatarName) {
            $avatarPath = '/img/uploads/' . $user_id . '/avatar/' . $imageType . '/' . $avatarName;
        } else {
            if ($user['gender'] == 'male') {
                $avatarPath = '/img/design/accountLayout/' . $imageType . '/maleDefault.png';
            } else {
                $avatarPath = '/img/design/accountLayout/' . $imageType . '/womanDefault.png';
            }

        }


        return $avatarPath; //path for avatar
    }

    public function getFriends($user_id)
    {
        $dbUserRelations = new Application_Model_DbTable_Friends();
        $userRelations = $dbUserRelations->getUserRelations($user_id);
        $UserModel = new Application_Model_DbTable_Users();
        $friends = array();
        foreach ($userRelations as $index => $value) {
            $friendId = ($user_id == $value['friend_id']) ? $value['sender_id'] : $value['friend_id'];
            $friends[$index] = $UserModel->getUserInfo($friendId);
            $friends[$index]['status'] = '1';
            if ($value['sender_id'] == $user_id && $value['sender_status'] == 2) {
                $friends[$index]['status'] = '2';
            }
            if ($value['friend_id'] == $user_id && $value['recipient_status'] == 2) {
                $friends[$index]['status'] = '2';
            }


        }
        return $friends; //get friends firstname, lastname,username, id

    }


    public function overwriteSession($userId)
    {
        $userTable = new Application_Model_DbTable_Users();
        $user = $userTable->getItem($userId);
        if($user['role'] == 1) {
            $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

            $authAdapter->setTableName('user')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password')
                ->setIdentity($user['username'])
                ->setCredential($user['password']);

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            $this->_helper->flashMessenger->addMessage(array('success'=>'Your account was successfully upgraded. Please make re-login on aimya to get additional features'));
            if ($result->isValid()) {
                Zend_Auth::getInstance()->clearIdentity();
                $identity = $authAdapter->getResultRowObject();
                $authStorage = $auth->getStorage();
                $authStorage->write($identity);
            } else {
                $this->view->passError = 'Wrong password!';
            }
        }
    }

}