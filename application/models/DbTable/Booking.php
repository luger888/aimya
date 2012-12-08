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
            'sender_status' => 1,
            'recipient_status' => 0,
            'booking_status' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        $this->insert($data);

    }

    public function getBookingByUser($userId)
    {
        $userId = (int)$userId;
        $row = $this->fetchAll(
            $this->select()
                ->where($this->getAdapter()->quoteInto('sender_id=?' , (int)$userId))
                ->orWhere($this->getAdapter()->quoteInto('recipient_id=?' , (int)$userId))
        );
        if (!$row) {
            throw new Exception("There is no element with ID: $userId");
        }
        $row = $row->toArray();
        return $row;
    }

    public function rejectBooking($id, $recipientId)
    {
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$id),
            $this->getAdapter()->quoteInto('recipient_id=?', (int)$recipientId)

        );
        $this->delete($where);

    }

    public function approveBooking($array = array(), $recipientId)
    {

        $data = array(
            'recipient_status' => 1,
            'booking_status' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $where = array(

            $this->getAdapter()->quoteInto('id=?', $array['booking_id']),
            $this->getAdapter()->quoteInto('recipient_id=?', $recipientId)

        );
        $this->update($data, $where);

    }

}