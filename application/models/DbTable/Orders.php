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
            'aimya_profit' => $array['aimya_profit'],
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

        $this->update($data , $where);
    }

    public function getPayKeyFromOrder($bookingId) {
        $bookingId = (int)$bookingId;

        $data = $this->select()
            ->from($this->_name, array('pay_key'))
            ->where('booking_id=?' , $bookingId);

        return $data->query()->fetch();
    }

    public function getLessonIncome(){

        $lastMonth = date("Y-m-d H:i:s", strtotime("-1 month"));
        $income = array();
        $total = $this->getAdapter()->select()
            ->from($this->_name,  array('sum(aimya_profit) as sum'))
            ->where('status=?', 'complete');
        $result = $total->query()->fetch();

        $totalLM = $this->getAdapter()->select()
          ->from($this->_name, array('sum(aimya_profit) as sum'))
            ->where('status=?', 'complete')
            ->where('created_at>=?', $lastMonth);
        $resultLM = $totalLM->query()->fetch();

        $income['allTime']['lessonIncome'] = $result;
        $income['lastMonth']['lessonIncome'] = $resultLM;

        return $income;
    }

    public function getUserIncome($perion = 'total') {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->getAdapter()->select()
            ->from($this->_name, array('user_profit' => 'SUM(teacher_profit)'))
            ->where($this->getAdapter()->quoteInto('seller_id=?' , $userId))
            ->where($this->getAdapter()->quoteInto('status=?' , 'complete'));
        if($perion != 'total') {
            if($perion == 'month') {
            $data->where('(updated_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND updated_at < NOW())');
            } else {
                $data->where('YEAR(updated_at) = YEAR(CURRENT_DATE)');
            }
        }

        return $data->query()->fetch();
    }

}