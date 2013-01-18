<?php
class Application_Model_DbTable_Availability extends Application_Model_DbTable_Abstract
{

    protected $_name = 'user_availability';

    public function updateAvailability($array = array(), $user_id)
    {
        $serializedArray = serialize($array);
        $data = array(

            'available_at' => $serializedArray

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$user_id);
        $this->update($data, $where);
    }

    public function createAvailability($user_id)
    {
        $data = array(

            'user_id' => (int)$user_id

        );

        $this->insert($data);
    }

    public function getAvailability($user_id)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchRow($this->select()->where('user_id=?' , (int)$user_id));
        $row = unserialize($row['available_at']);
        if($row == '0'){
            $row = array();
        }
        return $row;

    }

}
