<?php
class Application_Model_DbTable_Message extends Application_Model_DbTable_Abstract
{
    protected $_name = 'messages';

    public function sendMessage($array = array(), $recipient_status = 0){

        $data = array(
            'sender_id' => $array['sender_id'],
            'recipient_id' => $array['recipient_id'],
            'text' => $array['content'],
            'theme' => $array['subject'],
            'sender_status' => 1,
            'recipient_status' => $recipient_status,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->insert($data);
        if($insert) {
            return true;
        } else {
            return false;
        }

    }

    public function readMessage($messageId, $recipientId){
        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$messageId),
            $this->getAdapter()->quoteInto('recipient_id=?', (int)$recipientId)
        );
        $data = array(
            'recipient_status'=> 1,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->update($data, $where);
    }

    public function getInbox($userId) {

        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where('recipient_id=?' , $userId)
                ->where('recipient_status<?', 2)
                ->order((array('id DESC')))
                //->orWhere('recipient_status=?', 0)
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }

        return $row->toArray();
    }

    public function getSent($userId) {
        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where('sender_id=?' , $userId)
                ->where('sender_status<?', 2)
                ->order((array('id DESC')))
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }

        return $row->toArray();
    }

    public function getTrash($userId) {
        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where('(' . $this->getAdapter()->quoteInto('sender_id=?' , $userId) . ' AND ' . $this->getAdapter()->quoteInto('sender_status=?' , 2) . ') OR (' . $this->getAdapter()->quoteInto('recipient_id=?' , $userId) . ' AND ' . $this->getAdapter()->quoteInto('recipient_status=?' , 2) . ')')
                ->order((array('id DESC')))

        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }

        return $row->toArray();
    }

    public function getArchived($userId) {
        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where('(' . $this->getAdapter()->quoteInto('sender_id=?' , $userId) . ' AND ' . $this->getAdapter()->quoteInto('sender_status=?' , 3) . ') OR (' . $this->getAdapter()->quoteInto('recipient_id=?' , $userId) . ' AND ' . $this->getAdapter()->quoteInto('recipient_status=?' , 3) . ')')
                ->order((array('id DESC')))

        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }

        return $row->toArray();
    }

    public function deleteMessage($messageId, $action) {

        $array = array();

        if($action == 'sent') {
            $array['sender_status'] = 2;
        } elseif ($action == 'inbox') {
            $array['recipient_status'] = 2;
        } else {
            return false;
        }

        $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);;

        return $this->update($array , $where);
    }

    public function massTrash($messageId, $userId) {

        $data = array();
        $where = array();

        if($this->isSender($messageId, $userId)) {
            $data['sender_status'] = 2;
            $where[] = $this->getAdapter()->quoteInto('sender_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
        } else {
            $where[] = $this->getAdapter()->quoteInto('recipient_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
            $data['recipient_status'] = 2;
        }
        return $this->update($data , $where);
    }

    public function massDelete($messageId, $userId) {

        $data = array();
        $where = array();

        if($this->isSender($messageId, $userId)) {
            $data['sender_status'] = 4;
            $where[] = $this->getAdapter()->quoteInto('sender_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
        } else {
            $where[] = $this->getAdapter()->quoteInto('recipient_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
            $data['recipient_status'] = 4;
        }
        return $this->update($data , $where);
    }

    public function massArchive($messageId, $userId) {

        $data = array();
        $where = array();

        if($this->isSender($messageId, $userId)) {
            $data['sender_status'] = 3;
            $where[] = $this->getAdapter()->quoteInto('sender_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
        } else {
            $where[] = $this->getAdapter()->quoteInto('recipient_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
            $data['recipient_status'] = 3;
        }
        return $this->update($data , $where);
    }

    public function massRestore($messageId, $userId) {

        $data = array();
        $where = array();

        if($this->isSender($messageId, $userId)) {
            $data['sender_status'] = 1;
            $where[] = $this->getAdapter()->quoteInto('sender_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
        } else {
            $where[] = $this->getAdapter()->quoteInto('recipient_id=?', $userId);
            $where[] = $this->getAdapter()->quoteInto('id=?', $messageId);
            $data['recipient_status'] = 1;
        }
        return $this->update($data , $where);
    }

    public function checkNewMessage($userId) {
        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where('recipient_id=?' , $userId)
                ->where('sender_status=?', 0)
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }

        return $row->toArray();
    }

    public function getReplyMessage($messageId, $userId) {
        $userId = (int)$userId;
        $messageId = (int)$messageId;

        $data = $this->select()
            ->from($this->_name)
            ->where('id=?' , $messageId)
            ->where($this->getAdapter()->quoteInto('recipient_id=?', $userId) . ' OR ' . $this->getAdapter()->quoteInto('sender_id=?', $userId));

        return $data->query()->fetch();
    }

    public function getMessage($messageId, $userId) {
        $userId = (int)$userId;
        $messageId = (int)$messageId;

        $data = $this->select()
            ->from($this->_name)
            ->where('id=?' , $messageId)
            ->where($this->getAdapter()->quoteInto('recipient_id=?', $userId) . ' OR ' . $this->getAdapter()->quoteInto('sender_id=?', $userId));
            //->where('(' . 'recipient_id=?', $userId);

        return $data->query()->fetch();
    }

    public function getNewMessagesCount($userId) {
        $userId = (int)$userId;

        $data = $this->select()
            ->from($this->_name, array('id'=>'COUNT(*)'))
            ->where('recipient_status=?' , 0)
            ->where('recipient_id=?', $userId);

        return $data->query()->fetch();
    }

    public function isSender($messageId, $userId) {

        $data = $this->select()
            ->from($this->_name)
            ->where('(' . $this->getAdapter()->quoteInto('sender_id=?', (int)$userId ) . ' OR ' . $this->getAdapter()->quoteInto('recipient_id=?', (int)$userId) . ')')
            ->where('id=?', (int)$messageId);

        $messade = $data->query()->fetch();

        if($messade['sender_id'] == $userId) {
            return true;
        } else {
            return false;
        }

    }

}
