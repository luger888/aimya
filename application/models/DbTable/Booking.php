<?php

class Application_Model_DbTable_Booking extends Application_Model_DbTable_Abstract
{

    protected $_name = 'booking';


    public function addBooking($array = array(), $userId)
    {

        $data = array(

            'sender_id' => (int)$userId,
            'recipient_id' => (int)$array['recipient_id'],
            'started_at' => preg_replace('#<(.*?)>#', '', date('Y-m-d H:i:s', strtotime($array['started_at']))),
            'focus_name' => preg_replace('#<(.*?)>#', '',$array['focus_name']),
            'creator_tz' => preg_replace('#<(.*?)>#', '',$array['creator_tz']),
            'duration' => preg_replace('#<(.*?)>#', '',$array['duration']),
            'rate' => preg_replace('#<(.*?)>#', '',$array['rate']),
            'add_info' => preg_replace('#<(.*?)>#', '',$array['add_info']),
            'video' => (int)$array['video'],
            'feedback' => (int)$array['feedback'],
            'notes' => (int)$array['notes'],
            'is_sender_teacher' =>(int)$array['is_sender_teacher'],
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
            ->where('booking_status=?' , 0)
            ->where('recipient_id=?', $userId);

        return $data->query()->fetch();
    }

    public function isTeacher($bookingId, $userId)
    {
        $data = $this->select()
            ->from($this->_name)
            ->where($this->getAdapter()->quoteInto('id=?', (int)$bookingId))
            ->where($this->getAdapter()->quoteInto('sender_id=?', (int)$userId))
            ->where($this->getAdapter()->quoteInto('is_sender_teacher=?', 1));

        $result = $data->query()->fetch();

        if(!$result) {
            $query = $this->select()
                ->from($this->_name)
                ->where($this->getAdapter()->quoteInto('id=?', (int)$bookingId))
                ->where($this->getAdapter()->quoteInto('recipient_id=?', (int)$userId))
                ->where($this->getAdapter()->quoteInto('is_sender_teacher=?', 0));
            $response = $query->query()->fetch();
            if($response) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
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
        $iterator = 0;
        foreach($bookings as $result) {
            $results[$iterator]['booking'] = $result;
            if($result['sender_id'] == $userId) {
                $userData = '';
                $userData = $this->getAdapter()->select()
                    ->from(array('user'), array('user.firstname', 'user.lastname', 'user.username', 'user.role', 'user.id'))
                    ->where($this->getAdapter()->quoteInto('id=?' , (int)$result['recipient_id']));
                    $results[$iterator]['userData'] = $userData->query()->fetch();
            } else {
                $userData = '';
                $userData = $this->getAdapter()->select()
                    ->from('user', array('firstname', 'lastname', 'username', 'role', 'user.id'))
                    ->where($this->getAdapter()->quoteInto('id=?' , (int)$result['sender_id']));
                $results[$iterator]['userData'] = $userData->query()->fetch();
            }
            $iterator++;
        }

        return $results;
    }

    public function changeStatus($bookingId)
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$bookingId),
            '1' => "(sender_id={$userId} OR recipient_id={$userId})"
        );

        //Zend_Debug::dump($where);
        $data = array(
            'booking_status'=> 2,
            'updated_at' => date('Y-m-d H:m:s')
        );

        $result = $this->update($data, $where);

        if($result) {
            return true;
        } else {
            return false;
        }

    }

    public function paymentStatus($bookingId, $status)
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$bookingId),
            '1' => "(sender_id={$userId} OR recipient_id={$userId})"
        );

        //Zend_Debug::dump($where);
        $data = array(
            'payment_status'=> $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $result = $this->update($data, $where);

        if($result) {
            return true;
        } else {
            return false;
        }

    }

    public function payLesson($bookingId)
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$bookingId),
        );

        //Zend_Debug::dump($where);
        $data = array(
            'payment_status'=> 2,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $result = $this->update($data, $where);

        if($result) {
            return true;
        } else {
            return false;
        }

    }

    public function isExistBooking($booking_id, $userId)
    {
        $current = $this->fetchRow($this->select()->where('id=?',$booking_id ));
        $current_at = $current['started_at'];//12 am 13.12
        $current_duration = $current['duration']*60;
        $started_atAdd = date( "Y-m-d H:i:s", strtotime($current_at)+600+$current_duration );
        $started_atSub = date( "Y-m-d H:i:s", strtotime($current_at)-600-$current_duration );
        $row = $this->fetchRow($this->select()
            ->where('('. $this->getAdapter()->quoteInto('sender_id=?' , (int)$userId) . ') OR (' . $this->getAdapter()->quoteInto('recipient_id=?' , (int)$userId) . ')')
            ->where('started_at<=?', $started_atAdd )
            ->where('started_at>=?', $current_at)
            ->where('booking_status=?', 1)
           // ->where('id!=?', $booking_id )
            ->where('id!=?', $booking_id ));//if there is booking previous to current

        $row2 = $this->fetchRow($this->select()
            ->where('('. $this->getAdapter()->quoteInto('sender_id=?' , (int)$userId) . ') OR (' . $this->getAdapter()->quoteInto('recipient_id=?' , (int)$userId) . ')')
            ->where('started_at<=?', $current_at )
            ->where('started_at>=?', $started_atSub)
            ->where('booking_status=?', 1)
        // ->where('id!=?', $booking_id )
            ->where('id!=?', $booking_id ));//if there is booking previous to current
       // Zend_Debug::dump($current_at);
       // Zend_Debug::dump($started_atAdd);
        if($row || $row2){
            return true;
        }else{
            return false;
        }

    }

}