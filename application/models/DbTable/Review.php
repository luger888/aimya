<?php
class Application_Model_DbTable_Review extends Application_Model_DbTable_Abstract
{

    protected $_name = 'reviews';


    public function createReview($rating = NULL, $review = NULL, $lesson_id, $user_id, $bookingId, $creatorId)
    {
        $data = array(
            'booking_id' => (int)$bookingId,
            'recipient_id' => (int)$creatorId,
            'user_id' => (int)$user_id,
            'lesson_id' => (int)$lesson_id,
            'rating' => (int)$rating,
            'review' => $review,
            'user_id' => (int)$user_id,
            'review_date' => date('Y-m-d H:i:s')


        );

        return $this->insert($data);
    }

    public function getReviews($lesson_id)
    {
        $row = $this->fetchRow($this->select()->where('lesson_id=?', (int)$lesson_id));
        if ($row == '0') {
            $row = array();
        }
        return $row;

    }

    public function getReviewsByUser($user_id)
    {
        $array = $this->fetchAll($this->select()->where('recipient_id=?', (int)$user_id));

        if ($array == '0') {
            $array = array();
        }
        return $array->toArray();

    }

    public function getFullReviews($user_id, $from = NULL, $to = NULL)
    {
        $data = $this->getAdapter()->select()
            ->from($this->_name)
            ->joinLeft('booking', 'booking.id = reviews.booking_id', array('booking.focus_name', 'booking.rate', 'booking.duration', 'booking.started_at'))
            ->joinLeft('user', 'reviews.user_id = user.id', array('user.firstname', 'user.lastname', 'reviews.review_date'))
            ->where('(' . $this->getAdapter()->quoteInto('reviews.recipient_id=?', $user_id) . ') ');
        if ($from != NULL && $to != NULL) {
            $data->where('(' . $this->getAdapter()->quoteInto('reviews.review_date>=?', $from) . ') ')
                ->where('(' . $this->getAdapter()->quoteInto('reviews.review_date<=?', $to) . ') ');
        }


        return $data->query()->fetchAll();


    }

}
