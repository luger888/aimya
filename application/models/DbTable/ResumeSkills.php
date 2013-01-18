<?php
class Application_Model_DbTable_ResumeSkills extends Application_Model_DbTable_Abstract

{
    protected $_name = 'skills';

    public function createSkill($array = array(), $user_id){
        $data = array(

            'user_id' => (int)$user_id,
            'content' => $array['skill'],
            'file' => date('Y-m-d H:i:s')

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
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $data = $this->select()->where('user_id=?' , (int)$user_id)->order('id');
        $array = $data->query()->fetchAll();
        if($array == '0'){
            $array = array();
        }


        foreach ($array as $key =>$skill) {

            if (file_exists('./img/uploads/' . $identity->id . '/certificate/skill/' . $skill['id']) OR is_dir('./img/uploads/' . $identity->id . '/certificate/skill/' . $skill['id'])) {

                if ($handle = opendir('./img/uploads/' . $identity->id . '/certificate/skill/' . $skill['id'])) {
                    $i=0;
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry == '.' or $entry == '..') continue;
                        $array[$key]['certificate'][$i] = $entry;
                        $array[$key]['certificateUrl'][$i] = './img/uploads/' . $identity->id . '/certificate/skill/' . $skill['id'];
                        $i++;
                    }
                }
            }

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