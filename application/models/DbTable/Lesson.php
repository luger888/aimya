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
            'created_at' => date('Y-m-d H:m:s')
        );

        $this->insert($data);
    }

    public function checkAvailableLesson($userId){

        $data = $this   ->select()
            ->from($this->_name)
            ->where('partner_id='. (int)$userId)
            ->where('status=1');

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

        $data = array(
            'status'=> 2,
            'updated_at' => date('Y-m-d H:m:s')
        );

        $this->update($data, 'id='.$id);

    }

}