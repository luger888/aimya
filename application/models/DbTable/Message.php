<?php
class Application_Model_DbTable_Message extends Application_Model_DbTable_Abstract
{
    protected $_name = 'messages';

    public function sendMessage($array = array()){

        $data = array(
            'sender_id' => $array['sender_id'],
            'recipient_id' => $array['recipient_id'],
            'out' => $array['content'],
            'in' => $array['content'],
            'theme' => $array['subject'],
            'sender_status' => 1,
            'recipient_status' => 0,
            'status' => 0,
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')
        );

        $this->insert($data);
    }

    public function readMessage($messageId){
        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$messageId),
        );
        $data = array(
            'recipient_status'=> 1,
            'updated_at' => date('Y-m-d H:m:s')
        );
        $this->update($data, $where);
    }

    public function getInbox($userId) {

        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where('recipient_id=?' , $userId)
                ->where('recipient_status<?', 2)
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
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }

        return $row->toArray();
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

}
