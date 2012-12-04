<?php
class Application_Model_DbTable_UserRelations extends Application_Model_DbTable_Abstract
{
    protected $_name = 'user_relations';



    public function getUserRelations($userId)
    {
        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where('(' . $this->getAdapter()->quoteInto('sender_id=?' , $userId) . ') OR (' . $this->getAdapter()->quoteInto('friend_id=?' , $userId) . ')')
                ->where('sender_status=?' , 1)
                ->where('recipient_status=?' , 1)
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }

        return $row->toArray();
    }

    public function updateUserStatus($array = array(), $user_id)
    {

        $data = array(

            'sender_status'=> $array['status'],
            'updated_at' => date('Y-m-d H:i:s')

        );
        $where = array(

            $this->getAdapter()->quoteInto('friend_id=?', $array['updateUserId']),
            $this->getAdapter()->quoteInto('sender_id=?', $user_id)

        );
        $this->update($data, $where);
    }

}
