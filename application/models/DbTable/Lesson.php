<?php
class Application_Model_DbTable_Lesson extends Application_Model_DbTable_Abstract
{
    protected $_name = 'lessons';

    public function startLesson($array = array()){

        $data = array(

            'creator_id' => $array['creator_id'],
            'partner_id' => $array['partner_id'],
            'creator_stream_name' => $array['creator_stream_name'],
            'partner_stream_name' => $array['partner_stream_name'],
            'so_id' => $array['so_id'],
            'status' => $array['status'],
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->insert($data);
    }

    public function checkAvailableLesson($userId){

        $identity =  Zend_Auth::getInstance()->getStorage()->read();
        $data = $this->select()
            ->from($this->_name);
        if($identity->role == 1){
            $data->where('partner_id=?', (int)$userId);
        } else {
            $data->where('creator_id=?', (int)$userId);
        }
            $data->where('status=?', 1);

        $userData = $data->query();
        $row = $userData->fetch();
        if(!$row){
            return false;
        } else {
            return $row;
        }


    }

    public function changeStatus($id)
    {

        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$id),
        );

        $data = array(
            'status'=> 2,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->update($data, $where);

    }

}
