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

    public function updateResume($array, $id)
    {

        $data = array(

            'education'=> preg_replace('#<(.*?)>#', '', $array['education']),
            'degree' => preg_replace('#<(.*?)>#', '', $array['degree']),
            'address' => preg_replace('#<(.*?)>#', '', $array['address']),
            'telephone' => preg_replace('#<(.*?)>#', '', $array['phone'])
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

    public function getPayPalEmail($userId){

        $data = $this->select()
            ->from($this->_name, array('paypal_email'))
            ->where('user_id=?', (int)$userId);

        return $data->query()->fetch();
    }

    public function updatePaypalEmail($email, $userId){

        $data = array(
            'paypal_email'=> preg_replace('#<(.*?)>#', '', $email)
        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$userId);
        return $this->update($data, $where);

    }
}
