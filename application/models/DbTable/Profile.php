<?php
class Application_Model_DbTable_Profile extends Application_Model_DbTable_Abstract
{
    protected $_name = 'account';

    public function updateProfile($array, $id)
    {

        $data = array(

            'user_id' => $id,
            'add_info'=> $array['add_info'],
            'birthday' => $array['birthday'],
            'language' => $array['language'],
            'timezone' => '',
            'avatar' => '',
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')

        );

        $this->update($data, 'user_id='.$id);

    }

    public function getProfile($user_id){
        $user_id = (int)$user_id;
        $row = $this->fetchRow('user_id = ' . $user_id);
        if(!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }

    public function createProfile($array, $id){

        if(isset($array['birthday']))$zend = new Zend_Date($array['birthday']);
        $date = (isset($array['birthday'])) ? $zend->get('Y-M-d') : '';

        $data = array(

            'user_id' => $id,
            'birthday' => $date

        );

        $this->insert($data);

    }
}
