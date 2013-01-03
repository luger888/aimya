<?php

class Application_Model_DbTable_Subscriptions extends Application_Model_DbTable_Abstract
{

    protected $_name = 'subscription_history';

    public function createSubscription($userId, $aimyaProfit)
    {

        $data = array(
            'user_id' => (int)$userId,
            'aimya_profit' => (int)$aimyaProfit,
            'status' => 'paid',
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

    public function updateSubscription($subscriptionId, $status) {

        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where[] = $this->getAdapter()->quoteInto('id=?', $subscriptionId);;

        $this->update($data , $where);
    }

    public function getSubscription($userId) {

        $data = $this->select()
            ->from($this->_name, array('id'))
            ->where('user_id=?' , (int)$userId);

        return $data->query()->fetch();
    }
}