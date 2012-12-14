<?php

class Application_Model_DbTable_Users extends Application_Model_DbTable_Abstract
{

    protected $_name = 'user';

    #check email to unique

    public function checkByMail($email)
    {

        $data = $this->select()
            ->from('user', array('id', 'username', 'firstname', 'lastname', 'password'))
            ->where('email=?', $email);

        return $data->query()->fetch();

    }

    public function checkByUsername($username)
    {

        $data = $this->select()
            ->from('user', array('id', 'firstname', 'lastname', 'password'))
            ->where('username=?', $username);

        return $data->query()->fetch();

    }

    public function createUser($array = array())
    {


        $status = (isset($array['status'])) ? 1 : 0;
        $password = (isset($array['password'])) ? md5($array['password']) : '';
        $token = (isset($array['token'])) ? $array['token'] : '';


        $data = array(

            'email' => $array['email'],
            'password' => $password,
            'role' => (int)$array['role'],
            'username' => $array['username'],
            'firstname' => $array['firstname'],
            'lastname' => $array['lastname'],
            'gender' => $array['gender'],
            'status' => $status,
            'confirmation_token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        $this->insert($data);

    }

    #Approve by e-mail

    public function approve($mail, $query){

        $array = array(

            'status' => '1',
            'confirmation_token' => ''

        );

        $where[] = $this->getAdapter()->quoteInto('email=?', $mail);
        $where[] = $this->getAdapter()->quoteInto('confirmation_token=?', $query);

        return $this->update($array , $where);

    }

    #recoverPass

    public function recoverPass($data){

        $array = array(

            'password' => md5($data['password'])

        );
        $where = $this->getAdapter()->quoteInto('email=?', $data['email']);


        $this->update($array, $where);

    }

    public function getUser($user_id){
        $user_id = (int)$user_id;
        $row = $this->fetchRow($this->select()->where('id = ?', $user_id));
        if(!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }

    public function getUserInfo($user_id){
        $data = $this->select()
            ->from('user', array('id', 'firstname', 'lastname', 'username', 'timezone'))
            ->where('id=?', (int)$user_id);

        return $data->query()->fetch();
    }

    public function updateUser($array = array(), $id)
    {

        $data = array(

            'firstname'=> $array['firstname'],
            'lastname' => $array['lastname'],
            'gender' => $array['gender'],
            'timezone' => $array['timezone'],
            'email' => $array['email'],
            'username' => $array['username'],
            'updated_at' => date('Y-m-d H:i:s')

        );
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);

    }
    public function getLatestFeatured($role='0', $category = 'All'){


        $count  = 5;
        $offset = 0;
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $subQuery = $this->getAdapter()->select()
            ->from(array('service_detail'), array('MAX(updated_at)'))
            ->group('user_id')
            ->having('user_id = sd.user_id');

        $data = $this->getAdapter()->select()
            ->from(array('sd'=>'service_detail'), array('sd.lesson_category', 'sd.updated_at'))
            ->joinLeft('user', 'user.id = sd.user_id', array('user.id', 'user.firstname', 'user.lastname', 'user.username', 'user.role', 'user.timezone'))
            ->joinLeft('account', 'account.user_id = sd.user_id', array('account.add_info'))
            ->where('sd.updated_at=?', $subQuery)
            ->where('user.id<>?', $userId);
            if($role !== '0'){
                $data  ->where('user.role=?', $role);
            }
            if($category!=='All'){
                $data->where('sd.lesson_category=?', $category);
            }
            $data->order('sd.updated_at desc')
            ->limit($count, $offset);

        $author = $data->query()->fetchAll();

        foreach($author as $index=>$value){
            $serviceOffered = $this->getAdapter()
                ->select()
                ->from('service_detail')
                ->where('user_id=?', $value['id'])
                ->where('service_type=?', 1);

            $serviceRequested = $this->getAdapter()
                ->select()
                ->from('service_detail')
                ->where('user_id=?', $value['id'])
                ->where('service_type=?', 2);


            $servicesOffered = $serviceOffered->query()->fetchAll();
            $servicesRequested = $serviceRequested->query()->fetchAll();

            $author[$index]['service_offered'] = $servicesOffered;
            $author[$index]['service_requested'] = $servicesRequested;

        }

        return $author;
    }

    public function getFullData($id){

        $data = $this->getAdapter()
            ->select()
            ->from($this->_name)
            //->joinLeft('account', $where)
            ->joinLeft('account', 'account.user_id = user.id')
            ->where('user.id=?', $id);
        $services = $this->getAdapter()
            ->select()
            ->from('service_detail')
            ->where('service_detail.user_id=?', $id);

        $servicesResult = $services->query()->fetchAll();

        $result = $data->query()->fetch();
        $result['services'] = $servicesResult;
        $result['id'] = $id;

        return $result;
    }

    public function getTimeZone()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = $this->select()
            ->from('user', array('timezone'))
            ->where('id=?', (int)$userId);

        return $data->query()->fetch();

    }
}