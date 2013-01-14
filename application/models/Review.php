<?php

class Application_Model_Review
{
    public function getTotalReviews($user_id)
    {
        $reviewDb = new Application_Model_DbTable_Review();
        $reviews =  $reviewDb ->getReviewsByUser($user_id);
        $stars = array('five'=>0, 'four'=>0, 'three'=>0, 'two'=>0, 'one'=>0);
        foreach ($reviews as $review){
            switch ($review['rating']) {
                case 5:
                    $stars['five'] ++;
                    break;
                case 4:
                    $stars['four'] ++;
                    break;
                case 3:
                    $stars['three'] ++;
                    break;
                case 2:
                    $stars['two'] ++;
                    break;
                case 1:
                    $stars['one'] ++;
                    break;
            }
        }
        return $stars;

    }
    public function getFullReviews($user_id)
    {
        $reviewDb = new Application_Model_DbTable_Review();
        $reviews =  $reviewDb ->getReviewsByUser($user_id);

        return $reviews;
    }

}