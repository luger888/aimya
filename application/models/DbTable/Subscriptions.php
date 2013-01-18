<?php

class Application_Model_DbTable_Subscriptions extends Application_Model_DbTable_Abstract
{

    protected $_name = 'subscription_history';

    public function createSubscription($data = array())
    {

        $data = array(
            'user_id' => $data['user_id'],
            'aimya_profit' => $data['aimya_profit'],
            'pay_key' => $data['pay_key'],
            'status' => 'pending',
            'active_to' => $data['active_to'],
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

    public function updateSubscription($subscriptionId, $status)
    {

        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where[] = $this->getAdapter()->quoteInto('id=?', $subscriptionId);
        ;

        $this->update($data, $where);
    }

    public function getSubscription($userId)
    {

        $data = $this->select()
            ->from($this->_name, array('id'))
            ->where('user_id=?', (int)$userId);

        return $data->query()->fetch();
    }

    public function getLastId()
    {
        $data = $this->select()
            ->from($this->_name, array('MAX(id)'));

        return $data->query()->fetch();
    }

    public function getPayKeyFromOrder($subscriptionId)
    {
        $subscriptionId = (int)$subscriptionId;

        $data = $this->select()
            ->from($this->_name, array('pay_key'))
            ->where('id=?', $subscriptionId);

        return $data->query()->fetch();
    }

    public function getTimeLeft()
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->select()
            ->from($this->_name, array('active_to' => 'MAX(active_to)'))
            ->where('user_id=?', (int)$userId)
            ->where('(' . $this->getAdapter()->quoteInto('status=?', 'paid') . ') OR (' . $this->getAdapter()->quoteInto('status=?', 'trial') . ')');

        return $data->query()->fetch();

    }

    public function setDefaultPeriod()
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $date = date("Y-m-d h:i:s", strtotime("+5 day"));

        $data = array(
            'user_id' => (int)$userId,
            'aimya_profit' => 0,
            'pay_key' => '',
            'active_to' => $date,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        return $insert = $this->insert($data);

    }

    public function getSubscriptionsIncome()
    {
        $lastMonth = date("Y-m-d H:i:s", strtotime("-1 month"));
        $income = array();
        $total = $this->getAdapter()->select()
            ->from($this->_name, array('sum(aimya_profit) as sum'))
            ->where('status=?', 'paid');
        $result = $total->query()->fetch();

        $totalLM = $this->getAdapter()->select()
            ->from($this->_name, array('sum(aimya_profit) as sum'))
            ->where('status=?', 'paid')
            ->where('created_at>=?', $lastMonth);
        $resultLM = $totalLM->query()->fetch();

        $income['allTime']['subscrIncome'] = $result;
        $income['lastMonth']['subscrIncome'] = $resultLM;
        return $income;
    }

    public function getLatestSubscription($user_id)
    {
          $data = $this->select()
                ->from($this->_name, array(new Zend_Db_Expr('max(created_at) as maxId')))
                ->where('user_id =?', $user_id)
                ->where('status =?', 'paid');
        return $data->query()->fetch();
    }

    public function isRefundEnable()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = $this->getAdapter()->select()
            ->from($this->_name, array(new Zend_Db_Expr('max(created_at) as maxId'), 'id'))
            ->where( $this->getAdapter()->quoteInto('user_id=?', $userId))
            ->where( $this->getAdapter()->quoteInto('status=?', 'paid'));
        $result = $data->query()->fetch();

        $now = new DateTime(date("Y-m-d H:i:s"));

        $endDate = $result['maxId'];
        $ref = new DateTime($endDate);
        $diff = $now->diff($ref);
        if($diff->m > 0 || $diff->y > 0) {
            return $result['id'];
        }else{
            return false;
        }

    }

    public function getPayKeyFromSebscription($userId) {
        $userId = (int)$userId;

        $data = $this->select()
            ->from($this->_name, array('pay_key'))
            ->where('user_id=?' , $userId);

        return $data->query()->fetch();
    }

    public function updateSubscriptionStatus($userId) {

        $data = array(
            'status' => 'paid',
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where[] = $this->getAdapter()->quoteInto('user_id=?', $userId);;

        $this->update($data , $where);
    }

}