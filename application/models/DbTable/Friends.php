<?php

class Application_Model_DbTable_Friends extends Application_Model_DbTable_Abstract
{

    protected $_name = 'user_relations';

    public function getUserRelations($userId)
    {
        $userId = (int)$userId;

        $row = $this->fetchAll(
            $this->select()
                ->where($this->getAdapter()->quoteInto('sender_id=?' , $userId) . ' OR ' . $this->getAdapter()->quoteInto('friend_id=?' , $userId))
                ->where('(' . $this->getAdapter()->quoteInto('sender_status=?' , 1) . ') OR (' . $this->getAdapter()->quoteInto('sender_status=?' , 2) . ')')
                ->where('(' . $this->getAdapter()->quoteInto('recipient_status=?' , 1) . ') OR (' . $this->getAdapter()->quoteInto('recipient_status=?' , 2) . ')')
                ->group('id')
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }
        return $row->toArray();
    }

    public function updateUserStatus($array = array(), $user_id)
    {

        $friendField = $this->isInList($array['updateUserId']);

        if($friendField) {

            $where = $this->getAdapter()->quoteInto('id=?', $friendField['id']);

            if($friendField['sender_id'] == $user_id) {
                $data = array('sender_status'=> $array['status'], 'updated_at' => date('Y-m-d H:i:s'));
            } else {
                $data = array('recipient_status'=> $array['status'], 'updated_at' => date('Y-m-d H:i:s'));
            }

            $this->update($data, $where);
        }
    }
    public function deleteFriend($array = array()){
        $friendField = $this->isInList($array['updateUserId']);
        $where = $this->getAdapter()->quoteInto('id=?', $friendField['id']);
        $this->delete($where);
    }
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

    public function isPending($friendId)
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->select()
            ->from($this->_name)
            ->where($this->getAdapter()->quoteInto('sender_id=?' , $userId) . ' AND ' . $this->getAdapter()->quoteInto('friend_id=?' , $friendId) . ' AND ' . $this->getAdapter()->quoteInto('sender_status=?' , 1) . ' AND ' . $this->getAdapter()->quoteInto('recipient_status=?' , 0));
        $result = $data->query()->fetch();

        if ($result) {
            return true;
        } else {
            return false;
        }

    }

    public function addFriend($friendId, $message) {
        $status = false;
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $isInList = $this->isInList($friendId);
        $userTable = new Application_Model_DbTable_Users();
        $friend = $userTable->getItem($friendId);

        $user = $userTable->getItem($userId);
        $accountPage = Zend_Controller_Front::getInstance()->getBaseUrl() . "/user/{$userId}";
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
                    'content' => $message . ' My Account page is <a href="' . $accountPage . '">' . $user["username"] . '</a>' ,
                    'subject' => "Add to Friend Request",
                );

                if($message == '') {
                    $data['content'] = 'Hello ' . $friend['username'] . ', I have approved your request. My Account page is <a href="' . $accountPage . '">' . $userId . '</a>';
                }

                $messageTable->sendMessage($data);

                if ($status) {
                    return 'friend';
                } else {
                    return false;
                }

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
                        'content' => $message . ' My Account page is <a href="' . $accountPage . '">' . $user["username"] . '</a>. Or click <a href="' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/friends/send/?friend_id=' . $userId . '&url=' . $accountPage . '">this</a> to add this user to your account' ,
                        'subject' => "Add to Friend Request",
                    );
            $messageTable->sendMessage($data);
            if ($status) {
                return 'request';
            } else {
                return false;
            }
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

    public function getJoinedPeers($perion = 'total') {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->getAdapter()->select()
            ->from($this->_name, array('peers_count' => 'COUNT(updated_at)'))
            ->where('(' . $this->getAdapter()->quoteInto('sender_id=?' , $userId) . ' OR ' . $this->getAdapter()->quoteInto('friend_id=?' , $userId) . ')')
            ->where($this->getAdapter()->quoteInto('sender_status=?' , 1))
            ->where($this->getAdapter()->quoteInto('recipient_status=?' , 1));
            if($perion != 'total') {
                $data->where('(updated_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND updated_at < NOW())');
                /*} else {
                    $data->where('YEAR(updated_at) = YEAR(CURRENT_DATE)');
                }*/
            }

        return $data->query()->fetch();
    }
}