<?php
class Application_Model_DbTable_ResumeExperience extends Application_Model_DbTable_Abstract

{
    protected $_name = 'experience';

    public function createExperience($array = array(), $user_id){
        $data = array(

            'user_id' => (int)$user_id,
            'content' => $array['experience']

        );

        $this->insert($data);
        $lastId = $this->getAdapter()->lastInsertId();
        return $lastId;
    }

    public function updateExperience($array = array(), $user_id, $id)
    {

        $data = array(

            'experience' => $array['experience']

        );
        $where = $this->getAdapter()->quoteInto('user_id = ?', (int)$user_id);
        $this->update($data, $where);
    }

    public function getExperiences($user_id)
    {
        $array = $this->fetchAll($this->select()->where('user_id=?' , (int)$user_id));
        if(!$array) {
            throw new Exception("There is no element with ID: $user_id");
        }
        if($array == '0'){
            $array = array();
        }
        return $array;

    }
    public function deleteExperience($id, $user_id)
    {
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$id),
            $this->getAdapter()->quoteInto('user_id=?', (int)$user_id)

        );
        $this->delete($where);

    }

}