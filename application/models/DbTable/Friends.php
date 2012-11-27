<?php

class Application_Model_DbTable_Friends extends Application_Model_DbTable_Abstract
{

    protected $_name = 'user_relations';


    public function isFriend($friendId)
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->select()
            ->from($this->_name)
            ->where('(' . $this->getAdapter()->quoteInto('sender_id=?' , $userId) . ' AND ' . $this->getAdapter()->quoteInto('friend_id=?' , $friendId) . ') OR (' . $this->getAdapter()->quoteInto('sender_id=?' , $friendId) . ' AND ' . $this->getAdapter()->quoteInto('friend_id=?' , $userId) . ')')
            ->where('(' . $this->getAdapter()->quoteInto('sender_status=?' , 1) . ') AND (' . $this->getAdapter()->quoteInto('recipient_status=?' , 1) . ')');
        $result = $data->query()->fetch();

        if ($result) {
            return true;
        } else {
            return false;
        }

    }

    public function addFriend($friendId) {
        $status = false;
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $isInList = $this->isInList($friendId);
        $userTable = new Application_Model_DbTable_Users();
        $friend = $userTable->getItem($friendId);
        $accountPage = $this->baseLink . "/user/{$userId}";
        if($isInList){
            if($isInList['recipient_status'] == 0 && $isInList['sender_status'] == 1) {
                $where = $this->getAdapter()->quoteInto('id = ?', (int)$isInList['id']);
                $data = array(
                    'recipient_status' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $status = $this->update($data, $where);

                $messageTable = new Application_Model_DbTable_Message();
                $data = array(
                    'sender_id' => $userId,
                    'recipient_id' => $friendId,
                    'content' => 'Hello ' . $friend["username"] . ', I have approved your request. My Account page is <a href="' . $accountPage . '">' . $friend["username"] . '</a>' ,
                    'subject' => "Add to Friend Request",
                );
                $messageTable->sendMessage($data);
            } else {
                $status = false;
            }
        } else {
            $data = array(
                'sender_id' => $userId,
                'friend_id' => $friendId,
                'sender_status' => 1,
                'recipient_status' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            $status = $this->insert($data);

            $messageTable = new Application_Model_DbTable_Message();
            $data = array(
                        'sender_id' => $userId,
                        'recipient_id' => $friendId,
                        'content' => 'Hello ' . $friend["username"] . ', I\'d like add you to my account. My Account page is <a href="' . $accountPage . '">' . $friend["username"] . '</a>' ,
                        'subject' => "Add to Friend Request",
                    );
            $messageTable->sendMessage($data);
        }

        if ($status) {
            return true;
        } else {
            return false;
        }

    }

    public function isInList($friendId) {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->select()
            ->from($this->_name)
            ->where('(' . $this->getAdapter()->quoteInto('sender_id=?' , $userId) . ' AND ' . $this->getAdapter()->quoteInto('friend_id=?' , $friendId) . ') OR (' . $this->getAdapter()->quoteInto('sender_id=?' , $friendId) . ' AND ' . $this->getAdapter()->quoteInto('friend_id=?' , $userId) . ')');
        $result = $data->query()->fetch();

        return $result;
    }
}