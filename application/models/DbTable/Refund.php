<?php

class Application_Model_DbTable_Refund extends Application_Model_DbTable_Abstract
{

    protected $_name = 'refund_history';

    public function createRefund($data = array())
    {

        $data = array(
            'subscription_id' => $data['subscription_id'],
            'user_id' => $data['user_id'],
            'period' => $data['period'],
            'refunded_money' => $data['refunded_money'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->insert($data);
        if ($insert) {
            return true;
        } else {
            return false;
        }

    }

    public function cancelRefund($subscriptionId, $status)
    {

        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where[] = $this->getAdapter()->quoteInto('id=?', $subscriptionId);


        $this->update($data, $where);
    }

    public function getRefunds($userId)
    {

        $data = $this->getAdapter()->select()
            ->from($this->_name, array('id'))
            ->joinLeft('user', 'user.id =' . $this->_name . '.user_id', array('user.firstname', 'user.lastname', 'user.username', 'user.role', 'user.created_at '))
            ->where('user_id=?', (int)$userId)
            ->where('status=?', 1);

        return $data->query()->fetch();
    }

    public function approveRefund($subscriptionId, $status)
    {

        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where[] = $this->getAdapter()->quoteInto('id=?', $subscriptionId);


        $this->update($data, $where);
    }

}