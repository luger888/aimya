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
            'status' => $status,
            'confirmation_token' => $token,
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')

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
            ->from('user', array('id', 'firstname', 'lastname', 'username'))
            ->where('id=?', (int)$user_id);

        return $data->query()->fetch();
    }

    public function updateUser($array = array(), $id)
    {

        $data = array(

            'firstname'=> $array['firstname'],
            'lastname' => $array['lastname'],
            'email' => $array['email'],
            'username' => $array['username'],
            'updated_at' => date('Y-m-d H:m:s')

        );
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);

    }
    public function getLatestFeatured(){
        $order = 'created_at';
        $count  = 5;
        $offset = 0;

        $data = $this->select()
            ->from('user', array('id', 'firstname', 'lastname', 'username'))
            ->order($order)
            ->limit($count, $offset);

        return $data->query()->fetchAll();

    }
    public function getFullData($id){

        $data = $this->getAdapter()
            ->select()
            ->from($this->_name)
            //->joinLeft('account', $where)
            ->joinLeft('account', 'account.user_id = user.id')
            ->where('user.id=?', $id);

        $result = $data->query()->fetch();
        $result['id'] = $id;

        return $result;
    }
}