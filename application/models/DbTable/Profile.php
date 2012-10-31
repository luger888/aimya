<?php
class Application_Model_DbTable_Profile extends Application_Model_DbTable_Abstract
{
    protected $_name = 'account';

    public function updateProfile($array, $id)
    {

        $data = array(

            'id'=> NULL,
            'user_id' => $id,
            'education' => 5,
            'degree'=>5,
            'add_info'=> 5,
            'birthday' => 5,
            'language' => 5,
            'timezone' => 5,
            'avatar' => 5,
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')

        );

        $this->insert($data);
    }
}
