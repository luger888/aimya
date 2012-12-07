<?php

class Application_Model_DbTable_Booking extends Application_Model_DbTable_Abstract
{

    protected $_name = 'booking';


    public function addBooking($array = array(), $userId)
    {

        $data = array(

            'sender_id' => (int)$userId,
            'recipient_id' => (int)$array['recipient_id'],
            'started_at' => $array['started_at'],
            'focus_name' => $array['focus_name'],
            'duration' => $array['duration'],
            'rate' => $array['rate'],
            'add_info' => $array['add_info'],
            'video' => (int)$array['video'],
            'feedback' => (int)$array['feedback'],
            'notes' => (int)$array['notes'],
            'sender_status' => (int)$userId,
            'recipient_status' => (int)$userId,
            'booking_status' => (int)$userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        $this->insert($data);

    }

    public function getBookingByUser($user_id)
    {
        $user_id = (int)$user_id;
        $row = $this->fetchAll(
            $this->select()
                ->where($this->getAdapter()->quoteInto('sender_id=?' , (int)$user_id))
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }

}