<?php
class Application_Model_DbTable_LessonFeedback extends Application_Model_DbTable_Abstract
{
    protected $_name = 'lesson_feedback';

    public function createDefaultFeedback($lessonId, $teacherId){

        $data = array(

            'lesson_id' => (int)$lessonId,
            'teacher_id' => (int)$teacherId,
            'status' => 0,
            'content' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        );

        return $this->insert($data);

    }

    public function createFeedback($lessonId, $teacherId, $content){

        $data = array(
            'status' => 1,
            'content' => preg_replace('#<(.*?)>#', '', $content),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $where = array(
            $this->getAdapter()->quoteInto('lesson_id=?', (int)$lessonId),
            $this->getAdapter()->quoteInto('teacher_id=?', (int)$teacherId)
        );

        return $this->update($data, $where);

    }

    public function getFeedbackByLesson($lessonId)
    {
        $lessonId = (int)$lessonId;
        $data = $this->select()
            ->from($this->_name)
            ->where('lesson_id=?' , $lessonId);

        return $data->query()->fetch();
    }

}
