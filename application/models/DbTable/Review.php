<?php
class Application_Model_DbTable_Review extends Application_Model_DbTable_Abstract
{

    protected $_name = 'reviews';


    public function createReview($rating=NULL, $review=NULL, $lesson_id, $user_id)
    {
        $data = array(

            'user_id' => (int)$user_id,
            'lesson_id' => (int)$lesson_id,
            'rate' => (int)$rating,
            'review' => $review,
            'user_id' => (int)$user_id,
            'review_date'=> date('Y-m-d H:i:s')


        );

        return $this->insert($data);
    }

    public function getReviews($lesson_id)
    {
        $row = $this->fetchRow($this->select()->where('lesson_id=?' , (int)$lesson_id));
        if(!$row) {
            //throw new Exception("There is no element with ID: $user_id");
        }
        if($row == '0'){
            $row = array();
        }
        return $row;

    }

}
