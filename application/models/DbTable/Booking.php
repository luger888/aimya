<?php

class Application_Model_DbTable_Booking extends Application_Model_DbTable_Abstract
{

    protected $_name = 'booking';


    public function addBooking($array = array(), $userId)
    {

        $data = array(

            'sender_id' => (int)$userId,
            'recipient_id' => (int)$array['recipient_id'],
            'started_at' => preg_replace('#<(.*?)>#', '',$array['started_at']),
            'focus_name' => preg_replace('#<(.*?)>#', '',$array['focus_name']),
            'duration' => preg_replace('#<(.*?)>#', '',$array['duration']),
            'rate' => preg_replace('#<(.*?)>#', '',$array['rate']),
            'add_info' => preg_replace('#<(.*?)>#', '',$array['add_info']),
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
                ->where('('. $this->getAdapter()->quoteInto('sender_id=?' , (int)$userId) . ') OR (' . $this->getAdapter()->quoteInto('recipient_id=?' , (int)$userId) . ')')
                ->where($this->getAdapter()->quoteInto('booking_status<?' , 2))
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

    public function approveBooking($id, $recipientId)
    {

        $data = array(
            'recipient_status' => 1,
            'booking_status' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $where = array(

            $this->getAdapter()->quoteInto('id=?', (int)$id),
            $this->getAdapter()->quoteInto('recipient_id=?', (int)$recipientId)

        );
        $this->update($data, $where);

    }

    public function getNewBookingCount($userId) {
        $userId = (int)$userId;

        $data = $this->select()
            ->from($this->_name, array('id'=>'COUNT(*)'))
            ->where('recipient_status=?' , 0)
            ->where('recipient_id=?', $userId);

        return $data->query()->fetch();
    }

    public function isTeacher($lessonId, $userId)
    {
        $data = $this->select()
            ->from($this->_name)
            ->where($this->getAdapter()->quoteInto('id=?', (int)$lessonId))
            ->where($this->getAdapter()->quoteInto('sender_id=?', (int)$userId))
            ->where($this->getAdapter()->quoteInto('is_sender_teacher=?', 1));

        $result = $data->query()->fetch();

        if($result) {
            return true;
        } else {
            return false;
        }

    }

    public function getFullBookingData($userId)
    {
        $userId = (int)$userId;
        $bookingData = $this->select()
            ->from($this->_name)
            ->where('('. $this->getAdapter()->quoteInto('sender_id=?' , (int)$userId) . ') OR (' . $this->getAdapter()->quoteInto('recipient_id=?' , (int)$userId) . ')')
            ->where($this->getAdapter()->quoteInto('booking_status=?' , 1));

        $bookings = $bookingData->query()->fetchAll();

        $results = array();
        foreach($bookings as $result) {
            $results['booking'] = $result;
            if($result['sender_id'] == $userId) {
                $userData = '';
                $userData = $this->getAdapter()->select()
                    ->from(array('user'), array('user.firstname', 'user.lastname', 'user.username', 'user.role'))
                    ->where($this->getAdapter()->quoteInto('id=?' , (int)$result['sender_id']));
                    $results['userData'] = $userData->query()->fetch();
            } else {
                $userData = '';
                $userData = $this->getAdapter()->select()
                    ->from('user', array('firstname', 'lastname', 'username', 'role'))
                    ->where($this->getAdapter()->quoteInto('id=?' , (int)$result['recipient_id']));
                $results['userData'] = $userData->query()->fetch();
            }
        }

        return $results;
    }

}