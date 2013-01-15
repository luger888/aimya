<?php
class Application_Model_DbTable_LessonDuration extends Application_Model_DbTable_Abstract
{
    protected $_name = 'lesson_duration';

    public function getLessonDurations(){

        $data = $this   ->select()
            ->from('lesson_duration', (array('id', 'duration', 'status')))
            ->where('status=?', 1);

        return $data->query()->fetchAll();

    }
    public function removeDuration($id)
    {
        $data = array(
            'status' => 0,
            'updated_at' => date('Y-m-d H:i:s')

        );
        $where = array(
            $this->getAdapter()->quoteInto('id=?', $id)

        );
        return $this->update($data, $where);

    }

    public function  addDurations($dur){

        for($i=0;$i<count($dur['durations']);$i++){
            $data = array(
                'duration' => (int)$dur['durations'][$i],
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')

            );

            $this->insert($data);
        }

    }
}
