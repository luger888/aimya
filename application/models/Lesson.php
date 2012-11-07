<?php

class Application_Model_Lesson
{
    public function createFlashParams($array = array()) {
        $result = array();
        if(is_array($array)) {
            foreach($array as $key=>$value) {
                $value = md5($value . time());
                $result[$key] = $value;
            }
            return $result;
        } else {
            return false;
       }



    }
}