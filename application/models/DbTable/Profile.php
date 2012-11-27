<?php
class Application_Model_DbTable_Profile extends Application_Model_DbTable_Abstract
{
    protected $_name = 'account';

    public function updateProfile($array, $id)
    {

        $data = array(

            'add_info'=> $array['add_info'],
            'birthday' => $array['birthday'],
            'language' => $array['language'],
            'timezone' => $array['timezone'],
            'updated_at' => date('Y-m-d H:i:s')

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$id);
        $this->update($data, $where);

    }
    public function updateAvatar($avatar, $id)
    {

        $data = array(

            'avatar'=> $avatar

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$id);
        $this->update($data, $where);

    }

    public function getProfile($user_id){
        $user_id = (int)$user_id;
        $row = $this->fetchRow($this->select()->where('user_id = ?', $user_id));
        if(!$row) {
            throw new Exception("There is no element with ID: $user_id");
        }

        return $row->toArray();
    }

    public function createProfile($id){

        $data = array(

            'user_id' => (int)$id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        $this->insert($data);

    }
    public function updateObjective($array, $user_id){

        $data = array(

            'objective'=> $array['objective']

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$user_id);
        $this->update($data, $where);
    }
}
