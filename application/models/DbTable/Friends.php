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
}