<?php
class Application_Model_DbTable_Notifications extends Application_Model_DbTable_Abstract

{
    protected $_name = 'notification_settings';

    public function createNotifications($user_id){
    $data = array(

        'user_id' => (int)$user_id

    );

    $this->insert($data);
    }

    public function updateNotifications($array = array(), $user_id)
    {
        $serializedArray = serialize($array);
        $data = array(

            'settings' => $serializedArray

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$user_id);
        $this->update($data, $where);
    }

    public function getNotifications($user_id)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchRow($this->select()->where('user_id=?' , (int)$user_id));

        $row = unserialize($row['settings']);

        if($row == '0'){
            $row = array();
        }
        return $row;

    }
}