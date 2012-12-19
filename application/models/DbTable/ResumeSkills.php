<?php
class Application_Model_DbTable_ResumeSkills extends Application_Model_DbTable_Abstract

{
    protected $_name = 'skills';

    public function createSkill($array = array(), $user_id){
        $data = array(

            'user_id' => (int)$user_id,
            'content' => $array['skill'],
            'file' => date('Y-m-d H:m:s')

        );

        $this->insert($data);
        $lastId = $this->getAdapter()->lastInsertId();
        return $lastId;
    }

    public function updateSkill($array = array(), $user_id)
    {

        $data = array(

            'content' => $array['updateskill']

        );
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$array['resumeItemId']),
            $this->getAdapter()->quoteInto('user_id=?', (int)$user_id)

        );
        $this->update($data, $where);
    }

    public function getSkills($user_id)
    {
        $array = $this->fetchAll($this->select()->where('user_id=?' , (int)$user_id)->order('id'));
        if(!$array) {
            throw new Exception("There is no element with ID: $user_id");
        }
        if($array == '0'){
            $array = array();
        }
        return $array;

    }
    public function deleteSkill($id, $user_id)
    {
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$id),
            $this->getAdapter()->quoteInto('user_id=?', (int)$user_id)

        );
        $this->delete($where);

    }

}