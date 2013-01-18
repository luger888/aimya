<?php

class Application_Model_DbTable_Refund extends Application_Model_DbTable_Abstract
{

    protected $_name = 'refund_history';

    public function createRefund($subId, $user_id)
    {

        $data = array(
            'subscription_id' => $subId,
            'user_id' => $user_id,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        return $this->insert($data);

    }

    public function cancelRefund($subscriptionId)
    {
        $where = $this->getAdapter()->quoteInto('subscription_id=?', $subscriptionId);

        $this->delete($where);
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

    public function approveRefund($subscriptionId)
    {

        $data = array(
            'status' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where[] = $this->getAdapter()->quoteInto('subscription_id=?', $subscriptionId);


        $this->update($data, $where);
    }

}