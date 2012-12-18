<?php
class Application_Model_DbTable_ResumeEducation extends Application_Model_DbTable_Abstract

{
    protected $_name = 'education';

    public function createEducation($array = array(), $user_id){
        $data = array(

            'user_id' => (int)$user_id,
            'content' => $array['education']

        );

        $this->insert($data);
        $lastId = $this->getAdapter()->lastInsertId();
        return $lastId;
    }

    public function updateEducation($array = array(), $user_id)
    {

        $data = array(

            'content' => $array['updateeducation']

        );
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$array['resumeItemId']),
            $this->getAdapter()->quoteInto('user_id=?', (int)$user_id)

        );
        $this->update($data, $where);
    }

    public function getEducations($user_id)
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
    public function deleteEducation($id, $user_id)
    {
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$id),
            $this->getAdapter()->quoteInto('user_id=?', (int)$user_id)

        );
        $this->delete($where);

    }

}