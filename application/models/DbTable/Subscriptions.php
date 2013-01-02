<?php

class Application_Model_DbTable_Subscriptions extends Application_Model_DbTable_Abstract
{

    protected $_name = 'subscription_history';

    public function createSubscription($array = array())
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = array(
            'user_id' => $userId,
            'aimya_profit' => $array['aimya_profit'],
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

    public function getSubscription() {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->select()
            ->from($this->_name, array('id'))
            ->where('user_id=?' , $userId);

        return $data->query()->fetch();
    }
}