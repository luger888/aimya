<?php

class Application_Model_DbTable_Subscriptions extends Application_Model_DbTable_Abstract
{

    protected $_name = 'subscription_history';

    public function createSubscription($data  = array())
    {

        $data = array(
            'user_id' => $data['user_id'],
            'aimya_profit' => $data['aimya_profit'],
            'pay_key' => $data['pay_key'],
            'status' => 'paid',
            'active_to' => $data['active_to'],
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

    public function getLastId() {
        $data = $this->select()
            ->from($this->_name, array('MAX(id)'));

        return $data->query()->fetch();
    }

    public function getPayKeyFromOrder($subscriptionId) {
        $subscriptionId = (int)$subscriptionId;

        $data = $this->select()
            ->from($this->_name, array('pay_key'))
            ->where('id=?' , $subscriptionId);

        return $data->query()->fetch();
    }

    public function getTimeLeft() {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->select()
            ->from($this->_name, array('active_to' => 'MAX(active_to)'))
            ->where('user_id=?' , (int)$userId)
            ->where('status=?' , 'paid');

        return $data->query()->fetch();

    }

    public function setDefaultPeriod() {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $date = date("Y-m-d h:i:s",strtotime("+5 day"));

        $data = array(
            'user_id' => (int)$userId,
            'aimya_profit' => 0,
            'pay_key' => '',
            'active_to' => $date,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->insert($data);

    }
}