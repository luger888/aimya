<?php
class Application_Model_DbTable_Profile extends Application_Model_DbTable_Abstract
{
    protected $_name = 'account';

    public function updateProfile($array, $id)
    {

        $data = array(

            'add_info'=> preg_replace('#<(.*?)>#', '', $array['add_info']),
            'birthday' => preg_replace('#<(.*?)>#', '', $array['birthday']),
            'language' => preg_replace('#<(.*?)>#', '', $array['language']),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$id);
        $this->update($data, $where);

    }
    public function updateAvatar($avatar, $id)
    {

        $data = array(

            'avatar'=> preg_replace('#<(.*?)>#', '', $avatar)

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$id);
        $this->update($data, $where);

    }

    public function deleteAvatar($id)
    {
        $data = array(

            'avatar'=> ''

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

            'objective'=> preg_replace('#<(.*?)>#', '', $array['objective'])

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$user_id);
        $this->update($data, $where);
    }
}
