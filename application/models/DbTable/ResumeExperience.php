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

    public function updateExperience($array = array(), $user_id)
    {

        $data = array(

            'content' => $array['updateexperience']

        );
        $where = array(

            $this->getAdapter()->quoteInto('id =?', (int)$array['resumeItemId']),
            $this->getAdapter()->quoteInto('user_id=?', (int)$user_id)

        );
        $this->update($data, $where);
    }

    public function getExperiences($user_id)
    {
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        $data = $this->select()->where('user_id=?' , (int)$user_id)->order('id');
        $array = $data->query()->fetchAll();
        if(!$array) {
            throw new Exception("There is no element with ID: $user_id");
        }
        if($array == '0'){
            $array = array();
        }


        foreach ($array as $key =>$skill) {

            if (file_exists('./img/uploads/' . $identity->id . '/certificate/experience/' . $skill['id']) OR is_dir('./img/uploads/' . $identity->id . '/certificate/experience/' . $skill['id'])) {

                if ($handle = opendir('./img/uploads/' . $identity->id . '/certificate/experience/' . $skill['id'])) {
                    $i=0;
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry == '.' or $entry == '..') continue;
                        $array[$key]['certificate'][$i] = $entry;
                        $array[$key]['certificateUrl'][$i] = './img/uploads/' . $identity->id . '/certificate/experience/' . $skill['id'];
                        $i++;
                    }
                }
            }

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