<?php
class Application_Model_DbTable_UserRelations extends Application_Model_DbTable_Abstract
{
    protected $_name = 'user_relations';



    public function getUserRelations($sender_id)
    {
        $sender_id = (int)$sender_id;
        $row = $this->fetchAll('sender_id=?' , $sender_id);
        if (!$row) {
            throw new Exception("There is no element with ID: $sender_id");
        }

        return $row->toArray();
    }


}
