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
            'booking_id' => $array['booking_id'],
            'status' => $array['status'],
            'created_at' => date('Y-m-d H:m:s')
        );

        return $this->insert($data);
    }

    public function checkAvailableLesson($userId){

        $data = $this->select()
            ->from($this->_name)
            ->where('(' . $this->getAdapter()->quoteInto('partner_id=?', (int)$userId ) . ' OR ' . $this->getAdapter()->quoteInto('creator_id=?', (int)$userId) . ')')
            ->where('status=?', 1);

        $userData = $data->query();
        $row = $userData->fetch();
        if(!$row){
            return false;
        } else {
            return $row;
        }


    }

    public function changeStatus($lessonId)
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$lessonId),
            '1' => "(creator_id={$userId} OR partner_id={$userId})"
        );

        //Zend_Debug::dump($where);
        $data = array(
            'status'=> 2,
            'updated_at' => date('Y-m-d H:m:s')
        );

        $result = $this->update($data, $where);

        if($result) {
            return true;
        } else {
            return false;
        }

    }

    public function changeFlashSize($lessonId, $flashSize)
    {

        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $where   = array(
            $this->getAdapter()->quoteInto('id=?', (int)$lessonId),
        );
        $isCreator = $this->isCreator($lessonId, $userId);


        //Zend_Debug::dump($where);
        $data = array(
            'updated_at' => date('Y-m-d H:m:s')
        );

        if($isCreator) {
            $data['creator_flash_size'] = (int)$flashSize;
        } else {
            $data['partner_flash_size'] = (int)$flashSize;
        }

        $result = $this->update($data, $where);

        if($result) {
            return true;
        } else {
            return false;
        }

    }

    public function getFlashSize($lessonId) {
        $userId = (int)Zend_Auth::getInstance()->getIdentity()->id;
        $lessonId = (int)$lessonId;
        $field = '';
        if($this->isCreator($lessonId, $userId)) {
            $field = 'creator_flash_size';
        } else {
            $field = 'partner_flash_size';
        }

        $data = $this->select()
            ->from($this->_name, array($field))
            ->where('id=?' , $lessonId);

        $result = $data->query()->fetch();

        return $result[$field];
    }

    public function isCreator($lessonId, $userId) {
        $userId = (int)$userId;
        $lessonId = (int)$lessonId;

        $data = $this->select()
            ->from($this->_name)
            ->where('id=?' , $lessonId)
            ->where('(' . $this->getAdapter()->quoteInto('creator_id=?' , $userId) . ' OR ' . $this->getAdapter()->quoteInto('partner_id=?' , $userId) .') ')
            ->where($this->getAdapter()->quoteInto('status=?' , 1));

        $result = $data->query()->fetch();

        if($result['creator_id'] == $userId) {
            return true;
        } else {
            return false;
        }
    }

    public function getStudentLessons() {
        $userId = (int)Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->getAdapter()->select()
            ->from($this->_name)
            ->joinLeft('booking', 'booking.id = lessons.booking_id', array('booking.started_at', 'booking.focus_name', 'booking.notes', 'booking.video', 'booking.feedback'))
            ->where('(' . $this->getAdapter()->quoteInto('creator_id=?' , $userId) . ' OR ' . $this->getAdapter()->quoteInto('partner_id=?' , $userId) .') ')
            ->where($this->getAdapter()->quoteInto('status=?' , 2))
            ->order((array('id DESC')));

        return $data->query()->fetchAll();
    }

    public function getLessonByUser($lessonId) {
        $userId = (int)Zend_Auth::getInstance()->getIdentity()->id;

        $data = $this->getAdapter()->select()
            ->from($this->_name)
            ->where('(' . $this->getAdapter()->quoteInto('creator_id=?' , $userId) . ' OR ' . $this->getAdapter()->quoteInto('partner_id=?' , $userId) .') ')
            ->where($this->getAdapter()->quoteInto('status=?' , 2))
            ->where($this->getAdapter()->quoteInto('id=?' , (int)$lessonId));

        return $data->query()->fetch();
    }

    public function getLessons(){
        $lastMonth = date("Y-m-d H:i:s", strtotime("-1 month"));
        $lessonsCount = array();
        $total = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT');
        $result = $total->query()->fetch();

        $totalLM = $this->getAdapter()->select()
            ->from($this->_name, 'COUNT(*) AS COUNT')
            ->where('created_at>=?', $lastMonth);
        $resultLM = $totalLM->query()->fetch();

        $lessonsCount['allTime']['total'] = $result['COUNT'];
        $lessonsCount['lastMonth']['total'] = $resultLM['COUNT'];
        return $lessonsCount;
    }
}
