<?php

class Application_Model_DbTable_Users extends Application_Model_DbTable_Abstract
{

    protected $_name = 'user';

    #check email to unique

    public function checkByMail($email)
    {

        $data = $this->select()
            ->from('user', array('id', 'firstname', 'lastname', 'password'))
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

    public function createUser($array)
    {


        $status = (isset($array['status'])) ? 1 : 0;
        $password = (isset($array['password'])) ? md5($array['password']) : '';
        $token = (isset($array['token'])) ? $array['token'] : '';


        $data = array(

            'email' => $array['email'],
            'password' => $password,
            'role' => (int)$array['type'],
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

        return $this->update($array, 'email = ? and confirmation_token = ?' , array($mail, $query));

    }

    #recoverPass

    public function recoverPass($data){

        $array = array(

            'password' => md5($data['password'])

        );

        $this->update($array, 'email = ?', $data['email']);

    }

    public function getUser($user_id){
        $user_id = (int)$user_id;
        $row = $this->fetchRow('id = ' . $user_id);
        if(!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }

    public function updateUser($array, $id)
    {

        $data = array(

            'firstname'=> $array['firstname'],
            'lastname' => $array['lastname'],
            'email' => $array['email'],
            'updated_at' => date('Y-m-d H:m:s')

        );

        $this->update($data, 'id='.$id);

    }
}