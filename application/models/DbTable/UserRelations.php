<?php
class Application_Model_DbTable_UserRelations extends Application_Model_DbTable_Abstract
{
    protected $_name = 'user_relations';



    public function getUserRelations($sender_id)
    {
        $sender_id = (int)$sender_id;
        $row = $this->fetchAll(
            $this->select()
                ->where('sender_id=?' , $sender_id)
                ->where('sender_status=?' , 1)
                ->where('recipient_status=?' , 1)
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $sender_id");
        }

        return $row->toArray();
    }

    public function updateUserStatus($array = array(), $user_id)
    {

        $data = array(

            'status'=> $array['status'],
            'updated_at' => date('Y-m-d H:m:s')

        );
        $where = array(

            $this->getAdapter()->quoteInto('friend_id=?', $array['updateUserId']),
            $this->getAdapter()->quoteInto('sender_id=?', $user_id)

        );
        $this->update($data, $where);
    }

}
