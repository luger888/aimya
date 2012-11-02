<?php
class Application_Model_DbTable_ServiceDetail extends Application_Model_DbTable_Abstract
{
    protected $_name = 'service_detail';

    public function addService($array, $id){

        $data = array(

            'user_id' => $id,
            'lesson_category' => $array['lesson_category'],
            'subcategory' => $array['subcategory'],
            'rate' => (int)$array['rate'],
            'duration' => $array['duration'],
            'description' => $array['description'],
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')

        );

        $this->insert($data);

    }
}
