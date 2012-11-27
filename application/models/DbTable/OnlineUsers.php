<?php
class Application_Model_DbTable_OnlineUsers extends Application_Model_DbTable_Abstract
{
    protected $_name = 'online_users';



    public function makeOnline($userId)
    {
        $userId = (int)$userId;
        $data = array(

            'status'=> 1,
            'timestamp' => date('Y-m-d H:i:s')

        );
        $where = array(
            $this->getAdapter()->quoteInto('user_id=?', $userId)
        );
        $this->update($data, $where);
    }

    public function makeOffline($userId)
    {
        $userId = (int)$userId;
        $data = array(
            'status'=> 0,
        );
        $where = array(
            $this->getAdapter()->quoteInto('user_id=?', $userId)
        );
        $this->update($data, $where);
    }

}
