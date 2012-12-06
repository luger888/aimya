<?php

class Application_Model_DbTable_Booking extends Application_Model_DbTable_Abstract
{

    protected $_name = 'booking';


    public function addBooking($array = array(), $id)
    {

        $data = array(

            'sender_id' => (int)$id,
            'recipient_id' => (int)$id,
            'started_at' => $array['started_at'],
            'focus_name' => $array['focus_name'],
            'duration' => (int)$id,
            'rate' => (int)$id,
            'video' => (int)$id,
            'feedback' => (int)$id,
            'notes' => (int)$id,
            'sender_status' => (int)$id,
            'recipient_status' => (int)$id,
            'booking_status' => (int)$id,
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