<?php

class Application_Model_DbTable_Orders extends Application_Model_DbTable_Abstract
{

    protected $_name = 'payment_history';

    public function addPayment($array = array())
    {

        $data = array(
            'payer_id' => $array['payer_id'],
            'seller_id' => $array['seller_id'],
            'booking_id' => $array['booking_id'],
            'aimya_profit' => $array['booking_id'],
            'teacher_profit' => $array['teacher_profit'],
            'pay_key' => $array['pay_key'],
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->insert($data);
        if($insert) {
            return true;
        } else {
            return false;
        }

    }

    public function updatePaymentStatus($bookingId) {

        $data = array(
            'status' => 'complete',
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where[] = $this->getAdapter()->quoteInto('booking_id=?', $bookingId);;

        return $this->update($data , $where);
    }

    public function getPayKeyFromOrder($bookingId) {
        $bookingId = (int)$bookingId;

        $data = $this->select()
            ->from($this->_name, array('pay_key'))
            ->where('booking_id=?' , $bookingId);

        return $data->query()->fetch();
    }
}